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

CRITICAL: Output ONLY raw JSON. No text before or after. No markdown fences.

{"message":"clear, helpful explanation max 150 words","intent":"health","doctors":[],"articles":[{$artIds}],"tests":[{$testIds}],"urgency":null,"navigation_steps":[],"follow_up":"a helpful follow-up question or null"}

RULES:
- Explain the test/condition clearly in simple language.
- Include ALL matching test IDs from the list above.
- Include ALL matching article IDs from the list above.
- Keep "doctors" as empty array [].
- End message with: "Please consult a qualified doctor for personalized advice."
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
     * Stage 1: LLM picks relevant department names.
     * Stage 2: PHP fetches doctors/articles/tests from those departments.
     * Stage 3: LLM writes the empathetic response message.
     */
    private function handleHealth(string $message, array $history): array
    {
        // ── Stage 1: department classification ───────────────────────────────
        $allDepts = $this->context->getDepartmentNames();
        $deptList = implode(', ', $allDepts);

        $classifyPrompt = <<<PROMPT
You are a medical triage assistant. Given a patient's message, identify which HelloMed hospital department(s) are most relevant.

Available departments: {$deptList}

Patient message: "{$message}"

Respond with ONLY a JSON array of department names from the list above that are relevant.
Maximum 3 departments. Use exact names. Example: ["Nutrition and Dietetics","Cardiology"]
If unsure, return the single most relevant department. Never return an empty array.
PROMPT;

        $deptRaw  = $this->ollama->generate($classifyPrompt);
        $deptPick = $this->extractJsonArray($deptRaw ?? '');

        // Validate picked names against actual departments
        $validDepts = array_filter(
            $deptPick,
            fn (string $name): bool => in_array($name, $allDepts, true),
        );

        Log::info('AI: Department classification', [
            'message'   => $message,
            'raw'       => $deptRaw,
            'picked'    => $deptPick,
            'valid'     => $validDepts,
        ]);

        // ── Stage 2: DB context from validated departments ───────────────────
        $dbContext = $this->context->buildForDepartments(
            $message,
            array_values($validDepts),
        );

        // ── Stage 3: LLM generates empathetic message ────────────────────────
        $doctors  = json_encode($dbContext['doctors'],  JSON_UNESCAPED_SLASHES);
        $articles = json_encode($dbContext['articles'], JSON_UNESCAPED_SLASHES);
        $tests    = json_encode($dbContext['tests'],    JSON_UNESCAPED_SLASHES);
        $docIds   = implode(', ', array_column($dbContext['doctors'],  'id'));
        $artIds   = implode(', ', array_column($dbContext['articles'], 'id'));
        $urgEmoji = '⚠️';

        $disclaimer = 'IMPORTANT: You are NOT a doctor. Never diagnose or prescribe.';

        $systemPrompt = <<<PROMPT
You are HelloMed Health Assistant — a warm, empathetic AI guide. {$disclaimer}

RELEVANT DOCTORS for this patient's condition:
{$doctors}

RELEVANT ARTICLES:
{$articles}

RELEVANT TESTS:
{$tests}

CRITICAL: Output ONLY raw JSON. No text before or after. No markdown.

Required JSON structure:
{"message":"empathetic response max 120 words ending with disclaimer","intent":"health","doctors":[{$docIds}],"articles":[{$artIds}],"tests":[],"urgency":"low","navigation_steps":[],"follow_up":"one follow-up question or null"}

RULES:
- "doctors": ONLY include IDs of doctors relevant to the patient's specific complaint. Do NOT include all doctors. Omit doctors from unrelated specialties.
- "urgency": "high" only for emergencies (chest pain/stroke/heavy bleeding/unconsciousness). "moderate" for concerning symptoms. "low" for mild/chronic. null if unclear.
- End "message" with: "Please consult a qualified doctor. This is not a medical diagnosis."
- "articles": only include relevant article IDs.
- "tests": only include relevant test IDs (can be empty []).
PROMPT;

        $maxHistory    = config('ai.context.max_history', 6);
        $recentHistory = array_slice($history, -$maxHistory);

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        foreach ($recentHistory as $turn) {
            $messages[] = ['role' => $turn['role'], 'content' => $turn['content']];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        $raw = $this->ollama->chat($messages);

        if (empty($raw)) {
            return $this->errorResponse();
        }

        return $this->parseHealthResponse($raw, $dbContext);
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
            'message'          => $parsed['message'] ?? '',
            'intent'           => 'health',
            'doctors'          => $doctors,
            'articles'         => $articles,
            'tests'            => $tests,
            'navigation_steps' => [],
            'urgency'          => $parsed['urgency']    ?? null,
            'follow_up'        => $parsed['follow_up']  ?? null,
            'error'            => false,
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    // Helpers
    // ══════════════════════════════════════════════════════════════════════════

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
