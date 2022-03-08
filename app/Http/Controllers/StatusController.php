<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use App\Services\StatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * @var StatusService statusService
     */
    private StatusService $statusService;
    /**
     * @var EventService eventService
     */
    private EventService $eventService;

    /**
     * __construct
     *
     * @param StatusService statusService
     * @param EventService eventService
     *
     * @return void
     */
    public function __construct(
        StatusService $statusService,
        EventService $eventService
    ) {
        $this->statusService = $statusService;
        $this->eventService = $eventService;
    }

    /**
     * findAll
     *
     * @return JsonResponse
     */
    public function findAll(Request $request) : JsonResponse
    {
        $result = $this->statusService->findAll();
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
        $result = $this->statusService->findById($id);
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
