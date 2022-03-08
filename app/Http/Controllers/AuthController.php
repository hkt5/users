<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use App\Services\LoginService;
use App\Services\PasswordConfirmationService;
use App\Services\RegisterService;
use App\Services\ResetPasswordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private EventService $eventService;
    private LoginService $loginService;
    private RegisterService $registerService;
    private PasswordConfirmationService $passwordConfirmationService;
    private ResetPasswordService $resetPasswordService;

    public function __construct(
        EventService $eventService,
        LoginService $loginService,
        RegisterService $registerService,
        PasswordConfirmationService $passwordConfirmationService,
        ResetPasswordService $resetPasswordService
    ) {
        $this->eventService = $eventService;
        $this->loginService = $loginService;
        $this->registerService = $registerService;
        $this->passwordConfirmationService = $passwordConfirmationService;
        $this->resetPasswordService = $resetPasswordService;
    }

    public function login(Request $request) : JsonResponse
    {
        $result = $this->loginService->login($request->all());
        $logdata = [
            'reason' => $result['code'],
            'message' => $result,
        ];
        $this->eventService->logEvent($request, $logdata);
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
        $this->eventService->logEvent($request, $logdata);
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
        $this->eventService->logEvent($request, $logdata);
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
        $this->eventService->logEvent($request, $logdata);
        return response()->json(
            ['content' => $result['content'], 'errors' => $result['errors'],],
            $result['code']
        );
    }

    public function reset(Request $request) : JsonResponse
    {
        $result = $this->resetPasswordService->reset($request->all());
        $logdata = [
            'reason' => $result['code'],
            'message' => $result,
        ];
        $this->eventService->logEvent($request, $logdata);
        return response()->json(
            ['content' => $result['content'], 'errors' => $result['errors'],],
            $result['code']
        );
    }
}
