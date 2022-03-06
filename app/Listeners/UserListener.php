<?php

namespace App\Listeners;

use App\Events\UserEvent;
use Illuminate\Support\Facades\Http;

class UserListener
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
     * @param  \App\Events\UserEvent  $event
     * @return void
     */
    public function handle(UserEvent $event)
    {
        Http::post(env('LOG_URL'), $event->data);
    }
}
