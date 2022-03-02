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
     * __construct
     *
     * @param StatusRepository repository
     *
     * @return void
     */
    public function __construct(StatusRepository $repository)
    {
        $this->repository = $repository;
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
            return [
                'content' => $statuses, 'errors' => null, 'code' => Response::HTTP_OK
            ];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return [
                'content' => null, 'errors' => ['exception' => $e->getMessage()],
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
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
                return [
                    'content' => $status, 'errors' => null,
                    'code' => Response::HTTP_OK
                ];
            } else {
                return [
                    'content' => null, 'errors' => null,
                    'code' => Response::HTTP_NOT_FOUND
                ];
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return [
                'content' => null, 'errors' => ['exception' => $e->getMessage()],
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }
    }
}
