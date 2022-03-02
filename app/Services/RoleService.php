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
     * __construct
     *
     * @param RoleRepository repository
     *
     * @return void
     */
    public function __construct(RoleRepository $repository)
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
            $roles = $this->repository->findAll()->toArray();
            return [
                'content' => $roles, 'errors' => null, 'code' => Response::HTTP_OK
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
            $role = $this->repository->findById($id);
            if ($role !== null) {
                return [
                    'content' => $role, 'errors' => null,
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
