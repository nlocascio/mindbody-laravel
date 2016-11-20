<?php
return [
    'site_ids' => explode(',', env('MINDBODY_SITEIDS', null)),

    'source_credentials' => [
        'username' => env('MINDBODY_SOURCE_USERNAME', null),
        'password' => env('MINDBODY_SOURCE_PASSWORD', null),
    ],

    // User credentials can be declared independently, otherwise we'll use the MINDBODY default of pre-pending
    // the Source Username with an underscore.
    'user_credentials' => [
        'username' => env('MINDBODY_USER_USERNAME', '_' . env('MINDBODY_SOURCE_USERNAME', null)),
        'password' => env('MINDBODY_USER_PASSWORD', env('MINDBODY_SOURCE_PASSWORD', null)),
    ],

    'wsdls' => [
        'AppointmentService' => "https://api.mindbodyonline.com/0_5/AppointmentService.asmx?WSDL",
        'ClassService'       => "https://api.mindbodyonline.com/0_5/ClassService.asmx?WSDL",
        'ClientService'      => "https://api.mindbodyonline.com/0_5/ClientService.asmx?WSDL",
//        'DataService'        => "https://api.mindbodyonline.com/0_5/DataService.asmx?WSDL",   // Most users will typically not need these
//        'FinderService'      => "https://api.mindbodyonline.com/0_5/FinderService.asmx?WSDL", //
        'SaleService'        => "https://api.mindbodyonline.com/0_5/SaleService.asmx?WSDL",
        'SiteService'        => "https://api.mindbodyonline.com/0_5/SiteService.asmx?WSDL",
        'StaffService'       => "https://api.mindbodyonline.com/0_5/StaffService.asmx?WSDL"
    ]
];
