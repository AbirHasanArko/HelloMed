<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ollama Configuration
    |--------------------------------------------------------------------------
    | Settings for the local Ollama AI inference server.
    | Make sure Ollama is running at the configured host before using the chat.
    */

    'ollama' => [
        'host'        => env('OLLAMA_HOST', 'http://localhost:11434'),
        'model'       => env('OLLAMA_MODEL', 'mistral'),
        'timeout'     => (int) env('OLLAMA_TIMEOUT', 60),
        'temperature' => (float) env('OLLAMA_TEMPERATURE', 0.3),
        'context_length' => (int) env('OLLAMA_CONTEXT_LENGTH', 4096),
    ],

    /*
    |--------------------------------------------------------------------------
    | RAG / Context Settings
    |--------------------------------------------------------------------------
    | Controls how many records are fetched from the database to feed the LLM.
    */

    'context' => [
        'max_doctors'  => 5,
        'max_articles' => 4,
        'max_tests'    => 4,
        'max_history'  => 6,   // number of past chat turns to include
    ],

];
