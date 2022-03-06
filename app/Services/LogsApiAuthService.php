<?php

namespace App\Services;

use App\Enums\RoleId;
use App\Enums\StatusId;
use App\Repositories\UserRepository;
use App\Services\ResponseService;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class LogsApiAuthService
{
    private UserRepository $userRepository;
    private ResponseService $responseService;

    public function __construct(
        UserRepository $userRepository,
        ResponseService $responseService
    ) {
        $this->userRepository = $userRepository;
        $this->responseService = $responseService;
    }

    /**
     * authLogsApi
     *
     * @param string token
     *
     * @return array
     */
    public function auth(string $token) : array
    {
        $data = [
            'uuid' => base64_decode($token),
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE
        ];
        $user = $this->userRepository->findAuthUser($data);
        if ($user == null) {
            return $this->responseService->response(null, null, Response::HTTP_UNAUTHORIZED);
        } else {
            $this->userRepository->updateExpiredToken(
                [
                    'id' => $user->id, 'date' => Carbon::now(),
                ]
            );
            return $this->responseService->response(null, null, Response::HTTP_OK);
        }
    }
}
