<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserService $userService;
    private EventService $eventService;

    public function __construct(
        UserService $userService,
        EventService $eventService
    ) {
        $this->userService = $userService;
        $this->eventService = $eventService;
    }

    /**
     * findUser
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function findUser(Request $request) : JsonResponse
    {
        $result = $this->userService->findUser($request->header('Baerer'));
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
     * updateEmail
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function updateEmail(Request $request) : JsonResponse
    {
        $result = $this->userService->updateEmail(
            $request->all(),
            $request->header('Baerer')
        );
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
     * updatePassword
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function updatePassword(Request $request) : JsonResponse
    {
        $result = $this->userService->updatePassword(
            $request->all(),
            $request->header('Baerer')
        );
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
