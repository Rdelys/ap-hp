<?php

return [
    'api_key' => env('ANTHROPIC_API_KEY'),
    'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
    'endpoint' => 'https://api.anthropic.com/v1/messages',
    'timeout' => 30,
];