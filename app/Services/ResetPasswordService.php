<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;

class ResetPasswordService
{
    private UserRepository $userRepository;
    private ResponseService $responseService;
    private EventService $eventService;

    public function __construct(
        UserRepository $userRepository,
        ResponseService $responseService,
        EventService $eventService
    ) {
        $this->userRepository = $userRepository;
        $this->responseService = $responseService;
        $this->eventService = $eventService;
    }
    public function reset(array $data) : array
    {
        $user = $this->userRepository->findByEmail($data['email']);
        if ($user == null) {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_NOT_ACCEPTABLE
            );
        } else {
            $bytes = openssl_random_pseudo_bytes(2);
            $password = bin2hex($bytes);
            $currentUser = $this->userRepository->updatePassword(
                [
                    'id' => $user->id,
                    'password' => $password,
                    'date' => Carbon::now(),
                    'login_attemps' => 0
                ]
            );
            $this->eventService->sendPasswordEmail(['password' => $password]);
            return $this->responseService->response(
                ['user' => $currentUser],
                null,
                Response::HTTP_OK
            );
        }
    }
}
