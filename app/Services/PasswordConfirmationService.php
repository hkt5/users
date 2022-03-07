<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\Response;

class PasswordConfirmationService
{
    /**
     * @var UserRepository userRepository
     */
    private UserRepository $userRepository;
    /**
     * @var ResponseService responseService
     */
    private ResponseService $responseService;

    /**
     * __construct
     *
     * @param UserRepository userRepository
     * @param ResponseService responseService
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        ResponseService $responseService
    ) {
        $this->userRepository = $userRepository;
        $this->responseService = $responseService;
    }

    /**
     * confirm
     *
     * @param array data
     *
     * @return array
     */
    public function confirm(array $data) : array
    {
        $user = $this->userRepository->confirmAccount($data);
        if ($user != null) {
            return $this->responseService->response(
                ['user' => $user],
                null,
                Response::HTTP_OK
            );
        } else {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_NOT_ACCEPTABLE
            );
        }
    }
}
