<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Log;

/**
 * Core orchestrator for the HelloMed AI Health Assistant.
 *
 * PIPELINE:
 * ─────────
 * HOWTO queries:
 *   1. PHP matches pre-defined workflow by trigger phrases (guaranteed correct URLs)
 *   2. LLM writes only a 1-2 sentence warm intro
 *   3. Steps returned directly from PHP
 *   Fallback: if no workflow matches → LLM generates full howto response
 *
 * HEALTH queries — Two-stage pipeline:
 *   Stage 1: LLM classifies which HelloMed departments are relevant (tiny task, very accurate)
 *   Stage 2: PHP fetches doctors from those departments + relevant articles/tests from DB
 *   Stage 3: LLM generates empathetic response (only writes message + urgency + follow_up)
 *   Result:  Doctor cards always come from live DB — any new doctor/dept is immediately available
 */
class AiChatService
{
    public function __construct(
        private readonly OllamaClient $ollama,
        private readonly ContextBuilder $context,
        private readonly SiteMapBuilder $siteMap,
    ) {}

    /**
     * Process a chat message and return a structured AI response.
     *
     * @param  string  $message
     * @param  array<array{role: string, content: string}>  $history
     * @return array<string, mixed>
     */
    public function chat(string $message, array $history = []): array
    {
        // ── Intent classification ────────────────────────────────────────────
        $isHowTo = SymptomMapper::isHowToQuery($message);
        $isInfo  = ! $isHowTo && SymptomMapper::isInfoQuery($message);
        $intent  = $isHowTo ? 'howto' : 'health';

        // ── HOWTO: try pre-defined workflow first (guaranteed correct URLs) ──
        if ($isHowTo) {
            $matched = $this->siteMap->findWorkflow($message);
            if ($matched !== null) {
                return $this->handleMatchedWorkflow($matched, $message);
            }
            return $this->handleHowtoLlm($message, $history);
        }

        // ── INFO: "What is X?" — direct DB search, no department classification ──
        if ($isInfo) {
            return $this->handleInfo($message, $history);
        }

        // ── HEALTH: two-stage pipeline ───────────────────────────────────────
        return $this->handleHealth($message, $history);

    }

    // ══════════════════════════════════════════════════════════════════════════
    // INFO handler — "What is X?" direct search
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Handle "What is X?" queries with a direct DB search on tests and articles.
     * No department classification needed — we search by the raw message terms.
     */
    private function handleInfo(string $message, array $history): array
    {
        $dbContext = $this->context->buildForInfo($message);

        $tests    = json_encode($dbContext['tests'],    JSON_UNESCAPED_SLASHES);
        $articles = json_encode($dbContext['articles'], JSON_UNESCAPED_SLASHES);
        $artIds   = implode(', ', array_column($dbContext['articles'], 'id'));
        $testIds  = implode(', ', array_column($dbContext['tests'],    'id'));

        $systemPrompt = <<<PROMPT
You are HelloMed Health Assistant. Answer the patient's medical/diagnostic question clearly and helpfully.

RELEVANT DIAGNOSTIC TESTS from HelloMed:
{$tests}

RELEVANT HEALTH ARTICLES:
{$articles}

CRITICAL RULES:
1. Output ONLY raw JSON. No text before or after. No markdown.
2. "message" must be plain readable text ONLY — no JSON, no data objects, no URLs inside it.
3. Explain the test/condition clearly in simple language. Max 150 words.
4. End "message" with: "Please consult a qualified doctor for personalized advice."
5. "follow_up": write ONE short follow-up question (e.g. "Would you like to know how to book this test at HelloMed?"). Do NOT write null unless nothing is relevant.

JSON schema (fill in real values):
{"message":"<your explanation here>","intent":"health","doctors":[],"articles":[{$artIds}],"tests":[{$testIds}],"urgency":null,"navigation_steps":[],"follow_up":"<your follow-up question here>"}

RULES:
- Include ALL matching test IDs from the list above.
- Include ALL matching article IDs from the list above.
- Keep "doctors" as empty array [].
PROMPT;

        $messages   = [['role' => 'system', 'content' => $systemPrompt]];
        $messages[] = ['role' => 'user',   'content' => $message];
        $raw        = $this->ollama->chat($messages);

        if (empty($raw)) {
            return $this->errorResponse();
        }

        return $this->parseHealthResponse($raw, $dbContext);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // HOWTO handlers
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Matched workflow: steps come from PHP, LLM only writes the intro.
     */
    private function handleMatchedWorkflow(array $workflow, string $userMessage): array
    {
        $introPrompt = "You are HelloMed Health Assistant. The patient asked: \"{$userMessage}\"\n"
            . "Write a warm, friendly 1-2 sentence introduction for this guide: \"{$workflow['title']}\"\n"
            . "Output ONLY the intro sentence(s). No steps, no links, no JSON.";

        $intro = $this->ollama->generate($introPrompt);
        if (empty(trim($intro ?? ''))) {
            $intro = "Here's how to {$workflow['title']}:";
        }

        $steps = array_values(array_map(fn (array $s, int $i): array => [
            'step'        => $i + 1,
            'instruction' => $s['instruction'] ?? '',
            'link'        => $s['link']        ?? null,
            'link_text'   => $s['link_text']   ?? null,
        ], $workflow['steps'], array_keys($workflow['steps'])));

        return [
            'message'          => trim(strip_tags($intro)),
            'intent'           => 'howto',
            'doctors'          => [],
            'articles'         => [],
            'tests'            => [],
            'navigation_steps' => $steps,
            'urgency'          => null,
            'follow_up'        => null,
            'error'            => false,
        ];
    }

    /**
     * No workflow matched — ask the LLM to generate steps.
     * (Still uses site map context so LLM has real URLs to reference.)
     */
    private function handleHowtoLlm(string $message, array $history): array
    {
        $siteData = $this->siteMap->build();
        $nav      = json_encode($siteData['navigation'], JSON_UNESCAPED_SLASHES);
        $routes   = json_encode($siteData['routes'],     JSON_UNESCAPED_SLASHES);

        $systemPrompt = <<<PROMPT
You are HelloMed Health Assistant — a friendly AI guide for HelloMed Hospital's website.

NAVIGATION: {$nav}
ROUTES: {$routes}

CRITICAL: Respond with ONLY raw JSON (no markdown). Use ONLY URLs from the routes list above.

{"message":"1-2 sentence intro","intent":"howto","navigation_steps":[{"step":1,"instruction":"...","link":"https://...","link_text":"Go"}],"doctors":[],"articles":[],"tests":[],"urgency":null,"follow_up":null}
PROMPT;

        $messages   = [['role' => 'system', 'content' => $systemPrompt]];
        $messages[] = ['role' => 'user',   'content' => $message];
        $raw        = $this->ollama->chat($messages);

        if (empty($raw)) {
            return $this->errorResponse();
        }

        $parsed = $this->extractJson($raw);
        if ($parsed === null) {
            return ['message' => strip_tags($raw), 'intent' => 'howto', 'doctors' => [], 'articles' => [], 'tests' => [], 'navigation_steps' => [], 'urgency' => null, 'follow_up' => null, 'error' => false];
        }

        return [
            'message'          => $parsed['message']          ?? '',
            'intent'           => 'howto',
            'doctors'          => [],
            'articles'         => [],
            'tests'            => [],
            'navigation_steps' => $parsed['navigation_steps'] ?? [],
            'urgency'          => null,
            'follow_up'        => $parsed['follow_up']        ?? null,
            'error'            => false,
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    // HEALTH handler — two-stage pipeline
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Stage 1: LLM picks relevant department names (tiny JSON array — reliable).
     * Stage 2: PHP fetches doctors/articles/tests from those departments (live DB).
     * Stage 3: LLM writes ONLY plain text message + follow-up. PHP assembles cards.
     *
     * This completely eliminates JSON-in-message corruption — the LLM never touches
     * structured data in Stage 3.
     */
    private function handleHealth(string $message, array $history): array
    {
        // ── Stage 1: department classification ───────────────────────────────
        $allDepts = $this->context->getDepartmentNames();
        $deptList = implode(', ', $allDepts);

        $classifyPrompt = <<<PROMPT
You are a medical triage assistant. Identify the most relevant hospital department(s) for the patient's message.

Available departments: {$deptList}

Patient message: "{$message}"

Output ONLY a JSON array of department names. Max 3. Exact names only.
Example: ["Psychiatry"]
PROMPT;

        $deptRaw    = $this->ollama->generate($classifyPrompt);
        $deptPick   = $this->extractJsonArray($deptRaw ?? '');
        $validDepts = array_values(array_filter(
            $deptPick,
            fn (string $n): bool => in_array($n, $allDepts, true),
        ));

        Log::info('AI: Department classification', [
            'message' => $message,
            'picked'  => $validDepts,
        ]);

        // ── Stage 2: DB context from validated departments ───────────────────
        $dbContext = $this->context->buildForDepartments($message, $validDepts);

        // Build a short doctor name list for the prompt (names only — no JSON)
        // Build a human-readable doctor summary for the LLM (plain text — no JSON)
        $doctorSummary = '';
        foreach ($dbContext['doctors'] as $doc) {
            $fee  = ! empty($doc['online_fee'])  ? '৳' . $doc['online_fee'] . ' online'
                  : (! empty($doc['offline_fee']) ? '৳' . $doc['offline_fee'] . ' offline' : '');
            $exp  = ! empty($doc['experience_years']) ? $doc['experience_years'] . ' yrs exp.' : '';
            $line = "- {$doc['name']} — {$doc['specialty']}";
            if ($exp)  { $line .= ", {$exp}"; }
            if ($fee)  { $line .= ", {$fee}"; }
            $doctorSummary .= $line . "\n";
        }
        $deptNames = implode(' / ', $validDepts ?: ['our departments']);

        // ── Stage 3: LLM writes ONLY plain text (no JSON, no IDs, no URLs) ──
        $maxHistory    = config('ai.context.max_history', 6);
        $recentHistory = array_slice($history, -$maxHistory);

        $historyText = '';
        foreach ($recentHistory as $turn) {
            $role         = $turn['role'] === 'user' ? 'Patient' : 'Assistant';
            $historyText .= "{$role}: {$turn['content']}\n";
        }

        $plainPrompt = <<<PROMPT
You are HelloMed Health Assistant — warm, empathetic, and supportive.

{$historyText}Patient: {$message}

Available HelloMed specialists for this concern:
{$doctorSummary}
Write a caring, empathetic response (max 150 words):
- Acknowledge the patient's concern with empathy.
- Mention EVERY specialist listed above by name and specialty — do not skip any.
  Example: "Dr. Nazmul Huda, a Psychiatrist with 11 years of experience, and Abir Hasan Arko, a Neuropsychiatry specialist with 5 years of experience, are both available to help."
- End with: "Please consult a qualified doctor. This is not a medical diagnosis."
- Do NOT include links, URLs, IDs, or raw data.

FORMAT — output exactly 2 lines, nothing else:
MESSAGE: <your empathetic message>
FOLLOWUP: <one short follow-up question to better understand the patient, or NONE>
PROMPT;

        $raw = $this->ollama->generate($plainPrompt);

        if (empty($raw)) {
            return $this->errorResponse();
        }

        // ── Assemble response from plain-text output + DB data ───────────────
        [$msgText, $followUp] = $this->parsePlainResponse($raw);

        return [
            'message'          => $msgText,
            'intent'           => 'health',
            'doctors'          => $dbContext['doctors'],   // All dept-filtered doctors as cards
            'articles'         => $dbContext['articles'],
            'tests'            => $dbContext['tests'],
            'navigation_steps' => [],
            'urgency'          => $this->detectUrgency($message),
            'follow_up'        => $followUp,
            'error'            => false,
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    // Response parsing
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Parse the health-mode LLM response and enrich doctor/article/test IDs.
     *
     * @param  array<string, mixed>  $dbContext
     * @return array<string, mixed>
     */
    private function parseHealthResponse(string $raw, array $dbContext): array
    {
        $parsed = $this->extractJson($raw);

        if ($parsed === null) {
            Log::warning('AI: Could not parse JSON from health response', ['raw' => substr($raw, 0, 500)]);
            // Return prose message; articles/tests are pre-filtered so safe to include
            return [
                'message'          => strip_tags($raw),
                'intent'           => 'health',
                'doctors'          => [],
                'articles'         => $dbContext['articles'] ?? [],
                'tests'            => $dbContext['tests']    ?? [],
                'navigation_steps' => [],
                'urgency'          => null,
                'follow_up'        => null,
                'error'            => false,
            ];
        }

        // Enrich IDs → full objects
        $dbDoctors  = collect($dbContext['doctors']  ?? []);
        $dbArticles = collect($dbContext['articles'] ?? []);
        $dbTests    = collect($dbContext['tests']    ?? []);

        $docIds  = array_filter((array) ($parsed['doctors']  ?? []), 'is_numeric');
        $artIds  = array_filter((array) ($parsed['articles'] ?? []), 'is_numeric');
        $testIds = array_filter((array) ($parsed['tests']    ?? []), 'is_numeric');

        $doctors  = $dbDoctors->whereIn('id', $docIds)->values()->toArray();
        $articles = $dbArticles->whereIn('id', $artIds)->values()->toArray();
        $tests    = $dbTests->whereIn('id', $testIds)->values()->toArray();

        return [
            'message'          => $this->sanitiseMessage($parsed['message'] ?? ''),
            'intent'           => 'health',
            'doctors'          => $doctors,
            'articles'         => $articles,
            'tests'            => $tests,
            'navigation_steps' => [],
            'urgency'          => $parsed['urgency']    ?? null,
            'follow_up'        => $this->sanitiseFollowUp($parsed['follow_up'] ?? null),
            'error'            => false,
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    // Helpers
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Parse the LLM's plain-text Stage 3 response (LINE: prefix format).
     * Returns [messageText, followUp|null].
     *
     * Expected format:
     *   MESSAGE: <empathetic text>
     *   FOLLOWUP: <question> or NONE
     *
     * @return array{0: string, 1: string|null}
     */
    private function parsePlainResponse(string $raw): array
    {
        $lines   = array_map('trim', explode("\n", $raw));
        $message = '';
        $followUp = null;

        foreach ($lines as $line) {
            if (str_starts_with(strtoupper($line), 'MESSAGE:')) {
                $message = trim(substr($line, 8));
            } elseif (str_starts_with(strtoupper($line), 'FOLLOWUP:')) {
                $candidate = trim(substr($line, 9));
                $lower     = strtolower($candidate);
                if ($lower !== 'none' && $lower !== 'null' && $lower !== '') {
                    $followUp = $candidate;
                }
            }
        }

        // Fallback: if the LLM ignored the format, just use the whole raw text as the message
        if (empty($message)) {
            $message = trim(strip_tags($raw));
        }

        // Ensure disclaimer is present
        if (! str_contains($message, 'consult a qualified doctor')) {
            $message = rtrim($message, '.') . '. Please consult a qualified doctor. This is not a medical diagnosis.';
        }

        return [$message, $followUp];
    }

    /**
     * Detect urgency level from the patient message using keyword matching.
     * This is done in PHP so we don't rely on the LLM for structured output.
     *
     * @return 'high'|'moderate'|'low'|null
     */
    private function detectUrgency(string $message): ?string
    {
        $lower = strtolower($message);

        $high = [
            'chest pain', 'can\'t breathe', 'difficulty breathing', 'not breathing',
            'stroke', 'unconscious', 'collapsed', 'heavy bleeding', 'severe bleeding',
            'heart attack', 'cardiac arrest', 'emergency', 'ambulance',
        ];
        foreach ($high as $kw) {
            if (str_contains($lower, $kw)) {
                return 'high';
            }
        }

        $moderate = [
            'severe', 'unbearable', 'very bad', 'intense pain', 'high fever',
            'vomiting blood', 'cannot eat', 'cannot sleep', 'suicidal',
            'shortness of breath', 'passing out', 'fainting',
        ];
        foreach ($moderate as $kw) {
            if (str_contains($lower, $kw)) {
                return 'moderate';
            }
        }

        return 'low';
    }

    /**
     * Strip any JSON objects/arrays that leaked into the message string.
     * Mistral sometimes embeds raw doctor/article JSON inside the message field.
     */
    private function sanitiseMessage(string $message): string
    {
        // Remove JSON array blocks like [{"id":10,...}]
        $message = preg_replace('/\[\s*\{.*?\}\s*\]/s', '', $message) ?? $message;
        // Remove inline JSON objects like {"id":10,...}
        $message = preg_replace('/\{[^{}]*"id"\s*:\s*\d+[^{}]*\}/s', '', $message) ?? $message;
        // Clean up any leftover double spaces or ". ." artefacts
        $message = preg_replace('/\.\s*\./', '.', $message) ?? $message;
        $message = preg_replace('/\s{2,}/', ' ', $message) ?? $message;

        return trim($message);
    }

    /**
     * Reject literal placeholder texts that the LLM sometimes echoes verbatim.
     *
     * @param  string|null  $followUp
     * @return string|null
     */
    private function sanitiseFollowUp(mixed $followUp): ?string
    {
        if (empty($followUp) || ! is_string($followUp)) {
            return null;
        }

        $literals = [
            'one follow-up question or null',
            'a helpful follow-up question or null',
            '<your follow-up question here>',
            'null',
            'none',
        ];

        $lower = strtolower(trim($followUp));
        foreach ($literals as $literal) {
            if ($lower === $literal) {
                return null;
            }
        }

        return trim($followUp);
    }

    /**
     * Extract a JSON object from raw LLM output (handles markdown code fences).
     *
     * @return array<string, mixed>|null
     */
    private function extractJson(string $raw): ?array
    {
        $cleaned = preg_replace('/^```(?:json)?\s*/m', '', $raw);
        $cleaned = preg_replace('/```\s*$/m', '', $cleaned ?? $raw);
        $cleaned = trim($cleaned ?? $raw);

        $start = strpos($cleaned, '{');
        $end   = strrpos($cleaned, '}');

        if ($start === false || $end === false || $end < $start) {
            return null;
        }

        $data = json_decode(substr($cleaned, $start, $end - $start + 1), true);
        return is_array($data) ? $data : null;
    }

    /**
     * Extract a JSON array from raw LLM output.
     *
     * @return array<string>
     */
    private function extractJsonArray(string $raw): array
    {
        $cleaned = preg_replace('/^```(?:json)?\s*/m', '', $raw);
        $cleaned = preg_replace('/```\s*$/m', '', $cleaned ?? $raw);
        $cleaned = trim($cleaned ?? $raw);

        $start = strpos($cleaned, '[');
        $end   = strrpos($cleaned, ']');

        if ($start === false || $end === false || $end < $start) {
            return [];
        }

        $data = json_decode(substr($cleaned, $start, $end - $start + 1), true);
        return is_array($data) ? array_filter($data, 'is_string') : [];
    }

    /**
     * Standard error response when Ollama is unavailable.
     *
     * @return array<string, mixed>
     */
    private function errorResponse(): array
    {
        return [
            'message'          => 'The AI assistant is temporarily unavailable. Please try again in a moment.',
            'intent'           => 'error',
            'doctors'          => [],
            'articles'         => [],
            'tests'            => [],
            'navigation_steps' => [
                ['step' => 1, 'instruction' => 'Browse our doctors directory',    'link' => url('/doctors'),     'link_text' => 'Browse Doctors'],
                ['step' => 2, 'instruction' => 'Browse departments by specialty', 'link' => url('/departments'), 'link_text' => 'Browse Departments'],
            ],
            'urgency'   => null,
            'follow_up' => null,
            'error'     => true,
        ];
    }
}
