<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RoleService
{
    /**
     * @var RoleRepository repository
     */
    private RoleRepository $repository;

    /**
     * @var ResponseService service
     */
    private ResponseService $service;

    /**
     * __construct
     *
     * @param RoleRepository repository
     * @param ResponseService service
     *
     * @return void
     */
    public function __construct(
        RoleRepository $repository,
        ResponseService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * findAll
     *
     * @return array
     */
    public function findAll() : array
    {
        try {
            $roles = $this->repository->findAll()->toArray();
            return $this->service->response($roles, null, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->service->response(
                null,
                ['exception' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * findById
     *
     * @param int id
     *
     * @return array
     */
    public function findById(int $id) : array
    {
        try {
            $role = $this->repository->findById($id);
            if ($role !== null) {
                return $this->service->response($role, null, Response::HTTP_OK);
            } else {
                return $this->service->response(null, null, Response::HTTP_NOT_FOUND);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->service->response(
                null,
                ['exception' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
