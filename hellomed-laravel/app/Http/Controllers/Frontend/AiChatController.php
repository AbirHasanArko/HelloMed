<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Ai\AiChatService;
use App\Services\Ai\OllamaClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    public function __construct(
        private readonly AiChatService $aiChat,
        private readonly OllamaClient $ollama,
    ) {}

    /**
     * Process a chat message and return an AI response.
     *
     * POST /api/ai/chat
     *
     * Request body:
     *   message  string  required  The patient's message (max 1000 chars)
     *   history  array   optional  Previous [{role, content}] turns (max last 12)
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message'           => ['required', 'string', 'max:1000'],
            'history'           => ['sometimes', 'array', 'max:12'],
            'history.*.role'    => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:2000'],
        ]);

        try {
            $response = $this->aiChat->chat(
                $validated['message'],
                $validated['history'] ?? [],
            );

            return response()->json($response);
        } catch (\Throwable $e) {
            Log::error('AiChatController::chat error', ['error' => $e->getMessage()]);

            return response()->json([
                'message'          => 'Something went wrong. Please try again.',
                'intent'           => 'error',
                'doctors'          => [],
                'articles'         => [],
                'tests'            => [],
                'navigation_steps' => [],
                'urgency'          => null,
                'follow_up'        => null,
                'error'            => true,
            ], 500);
        }
    }

    /**
     * Check if Ollama is running and the configured model is available.
     *
     * GET /api/ai/chat/status
     */
    public function status(): JsonResponse
    {
        $available = $this->ollama->isAvailable();
        $models    = $available ? $this->ollama->listModels() : [];

        return response()->json([
            'available' => $available,
            'model'     => config('ai.ollama.model'),
            'models'    => $models,
            'host'      => config('ai.ollama.host'),
        ]);
    }

    /**
     * Store a thumbs-up/down feedback rating for an AI response.
     *
     * POST /api/ai/chat/feedback
     */
    public function feedback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string', 'max:64'],
            'rating'     => ['required', 'in:helpful,not_helpful'],
            'comment'    => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        try {
            \App\Models\AiChatFeedback::create([
                'user_id'    => auth()->id(),
                'session_id' => $validated['session_id'],
                'rating'     => $validated['rating'],
                'comment'    => $validated['comment'] ?? null,
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            // Non-critical — don't break the UI if feedback storage fails
            Log::warning('AiChatController::feedback error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false], 200);
        }
    }
}
