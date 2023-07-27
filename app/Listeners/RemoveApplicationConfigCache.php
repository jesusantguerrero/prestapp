<?php

namespace App\Listeners;

use App\Services\ApplicationConfigService;
use Illuminate\Auth\Events\Logout;

class RemoveApplicationConfigCache
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Logout $event)
    {
      (new ApplicationConfigService)->clear($event->user);
    }
}
