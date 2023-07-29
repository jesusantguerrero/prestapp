<?php

namespace App\Listeners;

use App\Events\Heartbeat;
use Illuminate\Support\Facades\Redis;

class HeartbeatListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Heartbeat $event)
    {
      Redis::publish('system', json_encode($event->announcement->toArray()));
    }
}
