<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * @var RoleService roleService
     */
    private RoleService $roleService;
    /**
     * @var EventService eventService
     */
    private EventService $eventService;

    /**
     * __construct
     *
     * @param RoleService roleService
     * @param EventService eventService
     *
     * @return void
     */
    public function __construct(
        RoleService $roleService,
        EventService $eventService
    ) {
        $this->roleService = $roleService;
        $this->eventService = $eventService;
    }

    /**
     * findAll
     *
     * @return JsonResponse
     */
    public function findAll(Request $request) : JsonResponse
    {
        $result = $this->roleService->findAll();
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
     * @param int id
     *
     * @return JsonResponse
     */
    public function findById(Request $request, int $id) : JsonResponse
    {
        $result = $this->roleService->findById($id);
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
