<?php

namespace App\Services;

use App\Events\UserEvent;
use Illuminate\Http\Request;

class EventService
{
    public function createUserEvent(Request $request, array $data) : void
    {
        new UserEvent($request, $data);
    }
}
