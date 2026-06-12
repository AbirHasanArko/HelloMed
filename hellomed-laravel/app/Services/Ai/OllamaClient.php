<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaClient
{
    private string $host;
    private string $model;
    private int $timeout;
    private float $temperature;
    private int $contextLength;

    public function __construct()
    {
        $this->host          = config('ai.ollama.host', 'http://localhost:11434');
        $this->model         = config('ai.ollama.model', 'mistral');
        $this->timeout       = config('ai.ollama.timeout', 60);
        $this->temperature   = config('ai.ollama.temperature', 0.3);
        $this->contextLength = config('ai.ollama.context_length', 4096);
    }

    /**
     * Send a prompt to Ollama and get the full response at once.
     */
    public function generate(string $prompt, ?string $model = null): string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->host}/api/generate", [
                    'model'  => $model ?? $this->model,
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature'    => $this->temperature,
                        'num_ctx'        => $this->contextLength,
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Ollama generate failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return '';
            }

            return $response->json('response', '');
        } catch (\Throwable $e) {
            Log::error('Ollama generate exception', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Send a chat-style prompt using the /api/chat endpoint (supports message history).
     *
     * @param  array<array{role: string, content: string}>  $messages
     */
    public function chat(array $messages, ?string $model = null): string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->host}/api/chat", [
                    'model'    => $model ?? $this->model,
                    'messages' => $messages,
                    'stream'   => false,
                    'options'  => [
                        'temperature' => $this->temperature,
                        'num_ctx'     => $this->contextLength,
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Ollama chat failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return '';
            }

            return $response->json('message.content', '');
        } catch (\Throwable $e) {
            Log::error('Ollama chat exception', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Check if the Ollama server is reachable and the configured model is available.
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->host}/api/tags");
            if ($response->failed()) {
                return false;
            }
            $models = collect($response->json('models', []))
                ->pluck('name')
                ->map(fn ($n) => explode(':', $n)[0]);

            return $models->contains($this->model) || $models->contains(explode(':', $this->model)[0]);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * List all models installed in Ollama.
     *
     * @return array<string>
     */
    public function listModels(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->host}/api/tags");
            return collect($response->json('models', []))->pluck('name')->toArray();
        } catch (\Throwable) {
            return [];
        }
    }

    public function getModel(): string
    {
        return $this->model;
    }
}
