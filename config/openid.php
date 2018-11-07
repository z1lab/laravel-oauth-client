<?php

return [
    'key' => env('KEY_PATH', storage_path('oauth-public.key')),
    'server' => env('AUTH_SERVER'),
    'register' => env('OPENID_REGISTER', false),
    'client' => [
        'id' => env('CLIENT_ID'),
        'secret' => env('CLIENT_SECRET')
    ]
];