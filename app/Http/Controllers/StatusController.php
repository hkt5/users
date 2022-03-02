<?php

namespace App\Http\Controllers;

use App\Services\StatusService;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    /**
     * @var StatusService service
     */
    private StatusService $service;

    /**
     * __construct
     *
     * @param StatusService service
     *
     * @return void
     */
    public function __construct(StatusService $service)
    {
        $this->service = $service;
    }

    /**
     * findAll
     *
     * @return JsonResponse
     */
    public function findAll() : JsonResponse
    {
        $data = $this->service->findAll();
        return response()->json(
            ['content' => $data['content'], 'errors' => $data['errors'],],
            $data['code']
        );
    }

    /**
     * findById
     *
     * @param int id
     *
     * @return JsonResponse
     */
    public function findById(int $id) : JsonResponse
    {
        $data = $this->service->findById($id);
        return response()->json(
            ['content' => $data['content'], 'errors' => $data['errors'],],
            $data['code']
        );
    }
}
