<?php

return [
    'provider' => env('METEOR_PROVIDER', 'nasa'),

    'nasa' => [
        'base_uri' => 'https://api.nasa.gov/neo/rest/v1/feed',
        'api_key'  => env('NASA_API_KEY')
    ]
];
