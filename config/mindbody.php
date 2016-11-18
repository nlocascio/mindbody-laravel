<?php
return [
    'test_mode' => true,
    'site_ids' => explode(',', env('MINDBODY_SITEIDS', null)),

    'source_username' => env('MINDBODY_SOURCE_USERNAME', null),
    'source_password' => env('MINDBODY_SOURCE_PASSWORD', null),

    // User credentials can be declared independently, otherwise we'll use the MINDBODY default of pre-pending
    // the Source Username with an underscore.
    'user_username' => env('MINDBODY_USER_USERNAME', '_' . env('MINDBODY_SOURCE_USERNAME', null)),
    'user_password' => env('MINDBODY_USER_PASSWORD', env('MINDBODY_SOURCE_PASSWORD', null))
];