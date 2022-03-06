<?php

namespace App\Services;

use App\Repositories\StatusRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StatusService
{
    /**
     * @var StatusRepository repository
     */
    private StatusRepository $repository;

    /**
     * @var ResponseService service
     */
    private ResponseService $service;

    public function __construct(
        StatusRepository $repository,
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
            $statuses = $this->repository->findAll()->toArray();
            return $this->service->response($statuses, null, Response::HTTP_OK);
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
            $status = $this->repository->findById($id);
            if ($status !== null) {
                return $this->service->response($status, null, Response::HTTP_OK);
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
