<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Services\UserAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    /**
     * @var UserAdminService userAdminService
     */
    private UserAdminService $userAdminService;
    /**
     * @var EventService eventService
     */
    private EventService $eventService;

    /**
     * __construct
     *
     * @param UserAdminService userAdminService
     * @param EventService eventService
     *
     * @return void
     */
    public function __construct(
        UserAdminService $userAdminService,
        EventService $eventService
    ) {
        $this->userAdminService = $userAdminService;
        $this->eventService = $eventService;
    }

    /**
     * findAll
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function findAll(Request $request) : JsonResponse
    {
        $result = $this->userAdminService->findAll($request->header('Baerer'));
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
     * findById
     *
     * @param Request request
     * @param int id
     *
     * @return JsonResponse
     */
    public function findById(Request $request, int $id) : JsonResponse
    {
        $result = $this->userAdminService->findById($request->header('Baerer'), $id);
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
     * create
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function create(Request $request) : JsonResponse
    {
        $result = $this->userAdminService->create($request->all(), $request->header('Baerer'));
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
     * @param int id
     *
     * @return JsonResponse
     */
    public function updateEmail(Request $request, int $id) : JsonResponse
    {
        $result = $this->userAdminService->updateEmail($request->all(), $request->header('Baerer'), $id);
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

    public function updateRoleandStatus(Request $request, int $id) : JsonResponse
    {
        $result = $this->userAdminService->updateRoleAndStatus($request->all(), $request->header('Baerer'), $id);
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
     * destroy
     *
     * @param Request request
     * @param int id
     *
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id) : JsonResponse
    {
        $result = $this->userAdminService->destroy($request->header('Baerer'), $id);
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
