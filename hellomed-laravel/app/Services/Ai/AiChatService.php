<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Log;

/**
 * Core orchestrator for the HelloMed AI Health Assistant.
 *
 * Routes messages between two modes:
 *   - 'health'  → RAG pipeline: DB context + Ollama → doctor/article suggestions
 *   - 'howto'   → Site map: workflow guides + Ollama → step-by-step navigation
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
     * @param  string  $message   The patient's latest message.
     * @param  array<array{role: string, content: string}>  $history  Previous turns.
     * @return array{
     *   message: string,
     *   intent: string,
     *   doctors: array,
     *   articles: array,
     *   tests: array,
     *   navigation_steps: array,
     *   urgency: string|null,
     *   follow_up: string|null,
     *   error: bool,
     * }
     */
    public function chat(string $message, array $history = []): array
    {
        // 1. Classify intent
        $intent = SymptomMapper::isHowToQuery($message) ? 'howto' : 'health';

        // 2. Build contextual data
        if ($intent === 'howto') {
            $contextData = $this->siteMap->build();
            $dbContext   = null;
        } else {
            $dbContext   = $this->context->build($message);
            $contextData = $dbContext;
        }

        // 3. Build system prompt
        $systemPrompt = $this->buildSystemPrompt($intent, $contextData, $dbContext);

        // 4. Assemble message history for Ollama
        $maxHistory = config('ai.context.max_history', 6);
        $recentHistory = array_slice($history, -$maxHistory);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];
        foreach ($recentHistory as $turn) {
            $messages[] = ['role' => $turn['role'], 'content' => $turn['content']];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        // 5. Call Ollama
        $raw = $this->ollama->chat($messages);

        if (empty($raw)) {
            return $this->errorResponse();
        }

        // 6. Parse and enrich
        return $this->parseResponse($raw, $intent, $dbContext);
    }

    /**
     * Build the system prompt based on intent and context.
     */
    private function buildSystemPrompt(string $intent, array $contextData, ?array $dbContext): string
    {
        $disclaimer = "IMPORTANT: You are NOT a doctor. Never diagnose or prescribe. Always recommend consulting a qualified healthcare professional.";

        if ($intent === 'howto') {
            $nav       = json_encode($contextData['navigation'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $routes    = json_encode($contextData['routes'] ?? [],    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $workflows = json_encode($contextData['workflows'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            return <<<PROMPT
You are HelloMed Health Assistant — a friendly AI guide for HelloMed Hospital's digital platform.

RIGHT NOW you are in WEBSITE GUIDE mode. The patient is asking HOW TO USE the website.

YOUR TASK:
- Provide clear, numbered, step-by-step instructions.
- Include exact clickable URLs from the site map for every relevant step.
- Be specific: mention exact button names, section names, and where to click.
- Keep the tone warm and helpful.

NAVIGATION BAR STRUCTURE:
{$nav}

FULL SITE ROUTES:
{$routes}

WORKFLOW GUIDES (use these as reference):
{$workflows}

RESPONSE FORMAT (JSON only, no markdown outside JSON):
{
  "message": "Your warm, concise intro (1-2 sentences)",
  "intent": "howto",
  "navigation_steps": [
    {"step": 1, "instruction": "...", "link": "https://...", "link_text": "Go here"},
    {"step": 2, "instruction": "...", "link": null, "link_text": null}
  ],
  "follow_up": "A helpful follow-up question or tip (optional, null if not needed)",
  "doctors": [],
  "articles": [],
  "tests": [],
  "urgency": null
}

RULES:
1. Only use URLs from the site map above. Never invent URLs.
2. Keep message under 100 words.
3. Provide 3-8 navigation steps.
4. If the question is vague, ask a clarifying follow-up question.
5. Always be encouraging and supportive.
PROMPT;
        }

        // ── HEALTH mode ───────────────────────────────────────────────────────
        $departments = json_encode($dbContext['departments'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $doctors     = json_encode($dbContext['doctors']     ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $articles    = json_encode($dbContext['articles']    ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $tests       = json_encode($dbContext['tests']       ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
You are HelloMed Health Assistant — a friendly, empathetic AI guide for HelloMed Hospital's digital platform.

RIGHT NOW you are in HEALTH ASSISTANT mode. The patient is describing symptoms or asking about a medical condition.

{$disclaimer}

AVAILABLE DEPARTMENTS AT HELLOMED:
{$departments}

AVAILABLE DOCTORS (from our database — only recommend these):
{$doctors}

RELEVANT HEALTH ARTICLES (only recommend these):
{$articles}

RELEVANT DIAGNOSTIC TESTS (only recommend these):
{$tests}

RESPONSE FORMAT (JSON only, no markdown outside JSON):
{
  "message": "Your warm, empathetic response (max 200 words)",
  "intent": "health",
  "doctors": [<array of doctor IDs from the list above that are relevant>],
  "articles": [<array of article IDs from the list above that are relevant>],
  "tests": [<array of test IDs from the list above that are relevant>],
  "urgency": "low" | "moderate" | "high" | null,
  "navigation_steps": [],
  "follow_up": "A clarifying follow-up question to gather more info (optional)"
}

RULES:
1. Be warm, empathetic, and reassuring.
2. Mention recommended doctors BY NAME and specialty.
3. Mention recommended articles BY TITLE.
4. Set urgency to "high" if symptoms suggest emergency (chest pain, difficulty breathing, stroke, heavy bleeding, unconsciousness).
5. ALWAYS include: "Please consult a qualified doctor. This is not a medical diagnosis."
6. Only recommend doctors/articles/tests that appear in the lists above. Use their exact IDs.
7. If no doctors match, set doctors to [] and suggest browsing → {$this->url('/doctors')}
8. Keep message under 200 words.
9. Ask a helpful follow-up question to better understand the patient's situation.
PROMPT;
    }

    /**
     * Parse Ollama's raw text response into a structured array.
     * Extracts the JSON block and enriches doctor/article/test IDs with full data.
     *
     * @param  array<string, mixed>|null  $dbContext
     * @return array<string, mixed>
     */
    private function parseResponse(string $raw, string $intent, ?array $dbContext): array
    {
        $parsed = $this->extractJson($raw);

        if ($parsed === null) {
            Log::warning('AI: Could not parse JSON from Ollama response', ['raw' => substr($raw, 0, 500)]);
            // Return the raw text as a plain message
            return [
                'message'          => strip_tags($raw),
                'intent'           => $intent,
                'doctors'          => [],
                'articles'         => [],
                'tests'            => [],
                'navigation_steps' => [],
                'urgency'          => null,
                'follow_up'        => null,
                'error'            => false,
            ];
        }

        // Enrich doctor/article/test IDs → full objects (for health mode)
        $doctors  = [];
        $articles = [];
        $tests    = [];

        if ($intent === 'health' && $dbContext !== null) {
            $docIds     = array_filter((array) ($parsed['doctors']  ?? []), 'is_numeric');
            $artIds     = array_filter((array) ($parsed['articles'] ?? []), 'is_numeric');
            $testIds    = array_filter((array) ($parsed['tests']    ?? []), 'is_numeric');

            $dbDoctors  = collect($dbContext['doctors']  ?? []);
            $dbArticles = collect($dbContext['articles'] ?? []);
            $dbTests    = collect($dbContext['tests']    ?? []);

            $doctors  = $dbDoctors->whereIn('id', $docIds)->values()->toArray();
            $articles = $dbArticles->whereIn('id', $artIds)->values()->toArray();
            $tests    = $dbTests->whereIn('id', $testIds)->values()->toArray();

            // If the AI returned IDs but they didn't match, include all context data
            if (empty($doctors) && ! empty($dbContext['doctors'])) {
                $doctors = $dbContext['doctors'];
            }
        }

        return [
            'message'          => $parsed['message'] ?? '',
            'intent'           => $parsed['intent']  ?? $intent,
            'doctors'          => $doctors,
            'articles'         => $articles,
            'tests'            => $tests,
            'navigation_steps' => $parsed['navigation_steps'] ?? [],
            'urgency'          => $parsed['urgency']    ?? null,
            'follow_up'        => $parsed['follow_up']  ?? null,
            'error'            => false,
        ];
    }

    /**
     * Attempt to extract a JSON object from the raw LLM output.
     * Handles cases where the model wraps JSON in markdown code fences.
     *
     * @return array<string, mixed>|null
     */
    private function extractJson(string $raw): ?array
    {
        // Strip markdown code fences if present
        $cleaned = preg_replace('/^```(?:json)?\s*/m', '', $raw);
        $cleaned = preg_replace('/```\s*$/m', '', $cleaned ?? $raw);
        $cleaned = trim($cleaned ?? $raw);

        // Find first { and last }
        $start = strpos($cleaned, '{');
        $end   = strrpos($cleaned, '}');

        if ($start === false || $end === false || $end < $start) {
            return null;
        }

        $json = substr($cleaned, $start, $end - $start + 1);
        $data = json_decode($json, true);

        return is_array($data) ? $data : null;
    }

    /**
     * Standard error response when Ollama is unavailable.
     *
     * @return array<string, mixed>
     */
    private function errorResponse(): array
    {
        return [
            'message'          => 'The AI assistant is temporarily unavailable. Please try again in a moment, or browse our doctors directly.',
            'intent'           => 'error',
            'doctors'          => [],
            'articles'         => [],
            'tests'            => [],
            'navigation_steps' => [
                ['step' => 1, 'instruction' => 'Browse our doctors directory', 'link' => url('/doctors'),     'link_text' => 'Browse Doctors'],
                ['step' => 2, 'instruction' => 'Browse departments',           'link' => url('/departments'), 'link_text' => 'Browse Departments'],
            ],
            'urgency'  => null,
            'follow_up'=> null,
            'error'    => true,
        ];
    }

    private function url(string $path): string
    {
        return url($path);
    }
}
