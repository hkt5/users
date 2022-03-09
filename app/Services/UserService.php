<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Validators\EmailValidator;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class UserService
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
     * findUser
     *
     * @param string uuid
     *
     * @return array
     */
    public function findUser(string $uuid) : array
    {
        $authUser = $this->userRepository->findByUuid(base64_decode($uuid));
        if ($authUser === null) {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_UNAUTHORIZED
            );
        } else {
            $responseUser = $this->userRepository->updateExpiredToken([
                'id' => $authUser->id, 'date' => Carbon::now(),
            ]);
            return $this->responseService->response(
                ['user' => $responseUser],
                null,
                Response::HTTP_OK
            );
        }
    }

    /**
     * updateEmail
     *
     * @param array data
     * @param string uuid
     *
     * @return array
     */
    public function updateEmail(array $data, string $uuid) : array
    {
        $authUser = $this->userRepository->findByUuid(base64_decode($uuid));
        if ($authUser === null) {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_UNAUTHORIZED
            );
        }
        $validator = Validator::make($data, EmailValidator::$rules);
        if ($validator->fails()) {
            return $this->responseService->response(
                null,
                $validator->errors()->toArray(),
                Response::HTTP_NOT_ACCEPTABLE
            );
        } else {
            $currentUser = $this->userRepository->updateEmail([
                'id' => $authUser->id, 'email' => $data['email'], 'date' => Carbon::now()
            ]);
            $responseUser = $this->userRepository->updateExpiredToken([
                'id' => $currentUser->id, 'date' => Carbon::now(),
            ]);
            return $this->responseService->response(
                ['user' => $responseUser],
                null,
                Response::HTTP_OK
            );
        }
    }
}
