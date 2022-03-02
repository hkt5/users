<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    private RoleService $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function findAll() : JsonResponse
    {
        $data = $this->service->findAll();
        return response()->json(
            ['content' => $data['content'], 'errors' => $data['errors'],],
            $data['code']
        );
    }

    public function findById(int $id) : JsonResponse
    {
        $data = $this->service->findById($id);
        return response()->json(
            ['content' => $data['content'], 'errors' => $data['errors'],],
            $data['code']
        );
    }
}
