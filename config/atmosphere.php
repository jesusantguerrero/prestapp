<?php

use App\Contracts\Features;

return [
  "superadmin" => [
    "email" => env('APP_SUPER_ADMIN', null)
  ],
  "backup" => [
    "email" => env('APP_BACKUP_EMAIL', null)
  ],
  "dropshipping" => [
    "serviceUrl" => "http://localhost:5000/api/v1/dropshipping"
  ],
  "sso" => [
    "url" => env('SSO_URL'),
    "key" => env("SSO_APP_KEY"),
    "secret" => env("SSO_APP_SECRET")
  ],
  "manager" => [
    "url" => env('SSO_URL'),
    "key" => env("SSO_APP_KEY"),
    "secret" => env("SSO_APP_SECRET")
  ],
  'features' => [
    Features::agentProfile(),
    Features::publicInvoices(),
    Features::clientPortal(['roles' => ['property-owner', 'tenant']]),
    // Features::companyProfile(),
    /* Features::listings([
       "applications" => true
       "application" => true
    ]),*/
    // Features::listings(),
],
];
