<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Services\LogsApiAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogsApiAuthController extends Controller
{    
    /**
     * @var LogsApiAuthService authService
     */
    private LogsApiAuthService $authService;    
    /**
     * @var EventService eventService
     */
    private EventService $eventService;

    public function __construct(
        LogsApiAuthService $authService,
        EventService $eventService
    ) {
        $this->authService = $authService;
        $this->eventService = $eventService;
    }
    
    /**
     * auth
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function auth(Request $request) : JsonResponse
    {
        $result = $this->authService->auth($request->header('Bareer'));
        $logdata = [
            'reason' => $result['code'],
            'message' => $result,
        ];
        $this->eventService->createUserEvent($request, $logdata);
        return response()->json(
            ['content' => $result['content'], 'errors' => $result['errors'],],
            $result['code']
        );
    }
}
