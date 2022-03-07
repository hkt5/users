<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use App\Services\LoginService;
use App\Services\PasswordConfirmationService;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private EventService $eventService;
    private LoginService $loginService;
    private RegisterService $registerService;
    private PasswordConfirmationService $passwordConfirmationService;

    public function __construct(
        EventService $eventService,
        LoginService $loginService,
        RegisterService $registerService,
        PasswordConfirmationService $passwordConfirmationService
    ) {
        $this->eventService = $eventService;
        $this->loginService = $loginService;
        $this->registerService = $registerService;
        $this->passwordConfirmationService = $passwordConfirmationService;
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

    public function confirm(Request $request, string $uuid) : JsonResponse
    {
        $result = $this->passwordConfirmationService->confirm(['uuid' => $uuid]);
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

    public function regenerate(Request $request, string $uuid) : JsonResponse
    {
        $result = $this->passwordConfirmationService->regenerateToken(['uuid' => $uuid]);
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
