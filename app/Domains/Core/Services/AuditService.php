<?php

namespace App\Domains\Core\Services;

use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Events\AuditCustom;

class AuditService {
    const RENT_INVOICE_NEW = "rent_invoice_new";
    const RENT_INVOICE_UPDATED = "rent_invoice_updated";
    const RENT_LATE_INVOICE_NEW = "rent_late_invoice_new";

    public static function dispatchCustomEvent(Model $model, $eventName, $newData = [], $oldData = []) {
      $model->auditEvent = $eventName;
      $model->isCustomEvent = true;
      $model->auditCustomOld = $oldData;
      $model->auditCustomNew = $newData;
      Event::dispatch(AuditCustom::class, [$model]);
    }
}
