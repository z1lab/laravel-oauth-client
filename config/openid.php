<?php

return [
    'key' => env('KEY_PATH', storage_path('oauth-public.key')),
    'server' => env('AUTH_SERVER', '127.0.0.3:8000'),
    'api_version' => env('API_VERSION', 'v1'),
    'register' => env('OPENID_REGISTER', false),
    'client' => [
        'id' => env('CLIENT_ID'),
        'secret' => env('CLIENT_SECRET')
    ]
];
