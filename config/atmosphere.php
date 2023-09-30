<?php

return [
  "superadmin" => [
    "email" => env('APP_SUPER_ADMIN', null)
  ],
  "backup" => [
    "email" => env('APP_BACKUP_EMAIL', null)
  ],
  "appProfiles" => [
    "agent" => "agent",
  ],
  "appProfilesDashboards" => [
    "agent" => ["general","properties", "loans"],
    "dropshipping" => ["general"]
  ],
  "dropshipping" => [
    "serviceUrl" => "http://localhost:5000/api/v1/dropshipping"
  ]
];
