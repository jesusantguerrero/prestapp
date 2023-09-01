<?php

return [
  "superadmin" => [
    "email" => env('APP_SUPER_ADMIN', null)
  ],
  "backup" => [
    "email" => env('APP_BACKUP_EMAIL', null)
  ],
  "dropshipping" => [
    "serviceUrl" => "http://localhost:5000/api/v1/dropshipping"
  ]
];
