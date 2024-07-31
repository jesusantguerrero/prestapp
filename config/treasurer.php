<?php

return [
  'driver' => env('TREASURER_DRIVER', 'neatlancer'),
  'drivers' => [
      'paypal' => [
        'driver' => 'pusher',
        'sandbox_client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'sandbox_secret' => env('PAYPAL_SANDBOX_SECRET', ''),
        'live_client_id' => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'live_secret' => env('PAYPAL_LIVE_SECRET', ''),
      ],
      'local' => [
          'driver' => 'local',
          'key' => env('TREASURER_KEY'),
      ],
      'neatlancer' => [
        'driver' => 'neatlancer',
        "url" => env('SSO_URL'),
        'client_id' => env("SSO_APP_KEY"),
        'secret_id' => env("SSO_APP_SECRET"),
      ],
  ],
  'settings' => [
      'mode' => env('PAYPAL_MODE', 'sandbox'),
      'http.ConnectionTimeOut' => 3000,
      'log.LogEnabled' => true,
      'log.FileName' => storage_path() . '/logs/treasurer.log',
      'log.LogLevel' => 'DEBUG'
  ],
  'implementation' => Insane\Journal\Models\Invoice\Invoice::class,
  "plans" => [
    [
        "name" => "plan_starter",
        "display_name" => "Starter",
        "features" => [
            "rent payments",
            "maintenance",
            "listings and applications",
            "leads tracking CRM",
        ],
        "allowed_guests" => 3,
        "units" => 50,
        "loans" => 20,
        "price" => 12,
        "public_plan" => true,
        "trial" => 14,
    ],
    [
        "name" => "plan_growth",
        "display_name" => "Growth",
        "features" => [
            "property manager tools",
            "management fees",
            "landlord forms",
            "property message board",
        ],
        "allowed_guests" => 5,
        "price" => 25,
        "public_plan" => true,
        "trial" => 14,
    ],
    [
      "name" => "plan_business",
      "display_name" => "Business",
      "features" => [
          "team management & tools",
          "task management",
          "customization",
          "team property permissions",
      ],
      "allowed_guests" => 5,
      "price" => 25,
      "public_plan" => true,
      "trial" => 14,
    ]
  ]
];
