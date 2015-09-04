<?php
return [
    'siteids' => explode(',', env('MINDBODY_SITEIDS', null)),

    'source'  => [
        'username' => env('MINDBODY_SOURCE_USERNAME', null),
        'password' => env('MINDBODY_SOURCE_PASSWORD', null)
    ],

    'user'    => [
        'username' => env('MINDBODY_USER_USERNAME', null),
        'password' => env('MINDBODY_USER_PASSWORD', null)
    ]
];