<?php
return [
    'siteids' => explode(',', env('MINDBODY_SITEIDS', null)),

    'source'  => [
        'username' => env('MINDBODY_SOURCE_USERNAME', null),
        'password' => env('MINDBODY_SOURCE_PASSWORD', null)
    ],

    // User credentials can be declared independently, otherwise we'll use the MINDBODY default of pre-pending
    // the Source Username with an underscore.
    'user'    => [
        'username' => env('MINDBODY_USER_USERNAME', '_' . env('MINDBODY_SOURCE_USERNAME', null)),
        'password' => env('MINDBODY_USER_PASSWORD', env('MINDBODY_SOURCE_PASSWORD', null))
    ]
];