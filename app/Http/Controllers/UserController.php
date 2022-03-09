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
            $request->header('Bareer')
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
