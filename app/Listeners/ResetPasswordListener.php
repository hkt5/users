<?php

namespace App\Listeners;

use App\Events\ResetPasswordEvent;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Mail;

class ResetPasswordListener
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
     * @param  \App\Events\ResetPasswordEvent  $event
     * @return void
     */
    public function handle(ResetPasswordEvent $event)
    {
        Mail::to($event->data['email'])->send(new ResetPassword($event->data));
    }
}
