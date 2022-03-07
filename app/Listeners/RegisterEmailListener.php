<?php

namespace App\Listeners;

use App\Events\RegisterEmailEvent;
use App\Mail\RegisterEmail;
use Illuminate\Support\Facades\Mail;

class RegisterEmailListener
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
     * @param  \App\Events\RegisterEmailEvent  $event
     * @return void
     */
    public function handle(RegisterEmailEvent $event)
    {
        Mail::to($event->data['email'])->send(new RegisterEmail($event->data));
    }
}
