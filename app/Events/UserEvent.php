<?php

namespace App\Events;

use Illuminate\Http\Request;

class UserEvent extends Event
{
    public array $postData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, array $data)
    {
        $this->postDatapostData = [
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
    }
}
