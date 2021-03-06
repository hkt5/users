<?php

namespace App\Services;

use App\Events\RegisterEmailEvent;
use App\Events\ResetPasswordEvent;
use App\Events\UserEvent;
use Illuminate\Http\Request;

class EventService
{
    /**
     * createUserEvent
     *
     * @param Request request
     * @param array data
     *
     * @return void
     */
    public function logEvent(Request $request, array $data) : void
    {
        $data = [
            'user' => -1,
            'base_path' => $request->getBasePath(),
            'client_ip' => $request->getClientIp(),
            'host' => $request->getHost(),
            'query_string' => $request->getQueryString(),
            'request_uri' => $request->getRequestUri() ?: 'no_user',
            'user_info' => $request->getUserInfo(),
            'reason' => $data['reason'],
            'message' => json_encode($data['message']),
        ];
        event(new UserEvent($data));
    }

    public function sendRegisterEmail(array $data) : void
    {
        event(new RegisterEmailEvent($data));
    }

    public function sendPasswordEmail(array $data) : void
    {
        event(new ResetPasswordEvent($data));
    }
}
