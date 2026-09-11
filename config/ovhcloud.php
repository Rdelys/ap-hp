<?php

return [
    'endpoint' => env('OVHCLOUD_AI_ENDPOINT', 'https://oai.endpoints.kepler.ai.cloud.ovh.net/v1/audio/transcriptions'),
    'token' => env('OVHCLOUD_AI_TOKEN'),
    'model' => env('OVHCLOUD_AI_MODEL', 'whisper-large-v3'),
    'langue' => env('OVHCLOUD_AI_LANGUE', 'fr'),
    'timeout' => 120,
];