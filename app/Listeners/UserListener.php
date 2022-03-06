<?php

namespace App\Listeners;

use App\Events\UserEvent;
use GuzzleHttp\Client;

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
        $client = new Client();
        $client->postAsync(env('LOG_URL'), $event->postData);
    }
}
