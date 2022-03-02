<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * @var RoleService service
     */
    private RoleService $service;

    /**
     * __construct
     *
     * @param RoleService service
     *
     * @return void
     */
    public function __construct(RoleService $service)
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
