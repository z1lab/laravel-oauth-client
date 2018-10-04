<?php

return [
    'key' => env('KEY_PATH', storage_path('oauth-public.key')),
    'server' => env('AUTH_SERVER'),
    'client' => [
        'id' => env('CLIENT_ID'),
        'SECRET' => env('CLIENT_SECRET')
    ]
];