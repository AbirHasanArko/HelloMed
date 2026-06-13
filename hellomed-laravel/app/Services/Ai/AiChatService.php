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

        // Build comma-separated ID lists so the example JSON is concrete
        $doctorIds  = implode(', ', array_column($dbContext['doctors']  ?? [], 'id'));
        $articleIds = implode(', ', array_column($dbContext['articles'] ?? [], 'id'));
        $testIds    = implode(', ', array_column($dbContext['tests']    ?? [], 'id'));

        return <<<PROMPT
You are HelloMed Health Assistant — a friendly, empathetic AI guide for HelloMed Hospital.

{$disclaimer}

AVAILABLE DEPARTMENTS:
{$departments}

AVAILABLE DOCTORS (only recommend doctors from this list, use their exact numeric id values):
{$doctors}

RELEVANT ARTICLES (only recommend articles from this list, use their exact numeric id values):
{$articles}

RELEVANT DIAGNOSTIC TESTS (only recommend tests from this list, use their exact numeric id values):
{$tests}

CRITICAL INSTRUCTION: You MUST respond with ONLY a valid JSON object. No prose before or after it.
Do NOT write any text outside the JSON. Do NOT use markdown fences. Output raw JSON only.

The JSON must have EXACTLY these keys:
- "message": string (warm empathetic response, max 150 words, include disclaimer at end)
- "intent": "health"
- "doctors": array of numeric IDs from the AVAILABLE DOCTORS list above (e.g. [{$doctorIds}])
- "articles": array of numeric IDs from the AVAILABLE ARTICLES list above (e.g. [{$articleIds}])
- "tests": array of numeric IDs from the AVAILABLE TESTS list above (e.g. [{$testIds}])
- "urgency": one of "low", "moderate", "high", or null
- "navigation_steps": [] (empty array for health mode)
- "follow_up": string or null (a helpful follow-up question)

EXAMPLE OUTPUT (structure only — use real data from the lists):
{"message": "I'm sorry to hear you're feeling this way. Based on your symptoms, I recommend seeing a specialist. Please consult a qualified doctor — this is not a medical diagnosis.", "intent": "health", "doctors": [{$doctorIds}], "articles": [{$articleIds}], "tests": [], "urgency": "low", "navigation_steps": [], "follow_up": "How long have you been experiencing these symptoms?"}

RULES:
1. Include ALL relevant doctor IDs from the list — not just one.
2. Set urgency "high" only for emergencies (chest pain, stroke, heavy bleeding, unconsciousness).
3. Always end message with: "Please consult a qualified doctor. This is not a medical diagnosis."
4. Never invent doctor/article/test IDs not in the lists above.
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
            // JSON parsing failed — AI wrote prose instead of JSON.
            // Still attach all DB context as cards so the user gets useful results.
            return [
                'message'          => strip_tags($raw),
                'intent'           => $intent,
                'doctors'          => ($intent === 'health') ? ($dbContext['doctors']  ?? []) : [],
                'articles'         => ($intent === 'health') ? ($dbContext['articles'] ?? []) : [],
                'tests'            => ($intent === 'health') ? ($dbContext['tests']    ?? []) : [],
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
