<?php

namespace App\Domains\CRM\Data;

use App\Domains\CRM\Enums\ClientStatus;
use Spatie\LaravelData\Data;

class ContactData extends Data {

 public function __construct(
    public int $user_id,
    public int $team_id,
    public string $names,
    public string $lastnames,
    public ?string $display_name,
    public string $dni,
    public string $dni_type,
    public ?string $email,
    public ?string $cellphone,
    public ?string $address_details,
    public ?string $work_name,
    public ?string $work_email,
    public ?string $work_cellphone,
    public ?string $work_address_details,
    public ?string $bank_name,
    public ?string $bank_account_number,
    public ?string $owner_distribution_date,
    public ClientStatus $status,
    public ?bool $is_lender,
    public ?bool $is_owner,
    public ?bool $is_tenant,
  ) {}
}
