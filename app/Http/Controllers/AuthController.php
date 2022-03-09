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
    /**
     * @var EventService eventService
     */
    /**
     * @var EventService eventService
     */
    private EventService $eventService;
    /**
     * @var LoginService loginService
     */
    private LoginService $loginService;
    /**
     * @var RegisterService registerService
     */
    private RegisterService $registerService;
    /**
     * @var PasswordConfirmationService passwordConfirmationService
     */
    private PasswordConfirmationService $passwordConfirmationService;
    /**
     * @var ResetPasswordService resetPasswordService
     */
    private ResetPasswordService $resetPasswordService;

    /**
     * __construct
     *
     * @param EventService eventService
     * @param LoginService loginService
     * @param RegisterService registerService
     * @param PasswordConfirmationService passwordConfirmationService
     * @param ResetPasswordService resetPasswordService
     *
     * @return void
     */
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

    /**
     * login
     *
     * @param Request request
     *
     * @return JsonResponse
     */
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

    /**
     * register
     *
     * @param Request request
     *
     * @return JsonResponse
     */
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

    /**
     * confirm
     *
     * @param Request request
     * @param string uuid
     *
     * @return JsonResponse
     */
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

    /**
     * regenerate
     *
     * @param Request request
     * @param string uuid
     *
     * @return JsonResponse
     */
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

    /**
     * reset
     *
     * @param Request request
     *
     * @return JsonResponse
     */
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
