<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use App\Services\LoginService;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private EventService $eventService;
    private LoginService $loginService;
    private RegisterService $registerService;

    public function __construct(
        EventService $eventService,
        LoginService $loginService,
        RegisterService $registerService
    ) {
        $this->eventService = $eventService;
        $this->loginService = $loginService;
        $this->registerService = $registerService;
    }

    public function login(Request $request) : JsonResponse
    {
        $result = $this->loginService->login($request->all());
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

    public function register(Request $request) : JsonResponse
    {
        $result = $this->registerService->registryNewUser($request->all());
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
