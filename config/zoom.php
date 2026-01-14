<?php

return [
    'api' => [
        'endpoint' => env('ZOOM_API_ENDPOINT', 'https://api.zoom.us/v2'),
        'timeout' => env('ZOOM_TIMEOUT', 60),
        'me' => env('ZOOM_ME_API', 'https://api.zoom.us/v2/users/me'),
    ],
    'oauth2' => [
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'redirect_uri' => env('ZOOM_REDIRECT_URI'),
        'authorize_uri' => env('ZOOM_AUTHORIZE_URI', 'https://zoom.us/oauth/authorize'),
        'token_uri' => env('ZOOM_TOKEN_URI', 'https://zoom.us/oauth/token'),
    ],
    'server_to_server' => [
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
    ],
];
