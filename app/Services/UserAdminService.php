<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Validators\EmailValidator;
use App\Validators\NewUserValidator;
use App\Validators\RoleAndStatusValidator;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class UserAdminService
{
    /**
     * @var UserRepository userRepository
     */
    /**
     * @var UserRepository userRepository
     */
    private UserRepository $userRepository;
    private ResponseService $responseService;
    private EventService $eventService;

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
        ResponseService $responseService,
        EventService $eventService
    ) {
        $this->userRepository = $userRepository;
        $this->responseService = $responseService;
        $this->eventService = $eventService;
    }

    /**
     * findAll
     *
     * @param string uuid
     *
     * @return array
     */
    public function findAll(String $uuid) : array
    {
        $currentUser = $this->userRepository->findByUuid(base64_decode($uuid));
        if ($currentUser === null) {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_UNAUTHORIZED
            );
        } else {
            $currentUser = $this->userRepository->updateExpiredToken([
                'id' => $currentUser->id, 'date' => Carbon::now(),
            ]);
            $users = $this->userRepository->findAll();
            return $this->responseService->response(
                [
                    'user' => $currentUser, 'users' => $users,
                ],
                null,
                Response::HTTP_OK
            );
        }
    }

    /**
     * findById
     *
     * @param string uuid
     * @param int id
     *
     * @return array
     */
    public function findById(string $uuid, int $id) : array
    {
        $user = $this->userRepository->findByUuid(base64_decode($uuid));
        if ($user === null) {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_UNAUTHORIZED
            );
        }
        $user = $this->userRepository->updateExpiredToken([
            'id' => $user->id, 'date' => Carbon::now(),
        ]);
        $currentUser = $this->userRepository->findById($id);
        if ($currentUser != null) {
            return $this->responseService->response(
                [
                'user' => $user, 'current_user' => $currentUser,
                ],
                null,
                Response::HTTP_OK
            );
        } else {
            return $this->responseService->response(
                [
                'user' => $user, 'current_user' => null,
                ],
                null,
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * create
     *
     * @param array data
     * @param string uuid
     *
     * @return array
     */
    public function create(array $data, string $uuid) : array
    {
        $user = $this->userRepository->findByUuid(base64_decode($uuid));
        if ($user === null) {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_UNAUTHORIZED
            );
        } else {
            $validator = Validator::make($data, NewUserValidator::$rules);
            if ($validator->fails()) {
                return $this->responseService->response(
                    null,
                    $validator->errors()->toArray(),
                    Response::HTTP_NOT_ACCEPTABLE
                );
            } else {
                $data['uuid'] = Uuid::uuid4();
                $data['login_attempts'] = 0;
                $data['is_confirmed'] = false;
                $data['expired_token'] = Carbon::now()->addMinutes(env('TOKEN_EXPIRE'));
                $data['last_password_changed'] = Carbon::now();
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $currentUser  = $this->userRepository->create($data);
                $this->eventService->sendRegisterEmail($currentUser->toArray());
                return $this->responseService->response(
                    ['user' => $user, 'current_user' => $currentUser],
                    null,
                    Response::HTTP_OK
                );
            }
        }
    }

    /**
     * updateEmail
     *
     * @param array data
     * @param string uuid
     * @param int id
     *
     * @return array
     */
    public function updateEmail(array $data, string $uuid, int $id) : array
    {
        $user = $this->userRepository->findByUuid(base64_decode($uuid));
        if ($user === null) {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_UNAUTHORIZED
            );
        } else {
            $validator = Validator::make($data, EmailValidator::$rules);
            if ($validator->fails()) {
                return $this->responseService->response(
                    null,
                    $validator->errors()->toArray(),
                    Response::HTTP_NOT_ACCEPTABLE
                );
            }
            $currentUser = $this->userRepository->findById($id);
            if ($currentUser === null) {
                return $this->responseService->response(
                    ['user' => $user, 'current_user' => $currentUser],
                    null,
                    Response::HTTP_NOT_FOUND
                );
            } else {
                $data['id'] = $currentUser->id;
                $data['date'] = Carbon::now();
                $currentUser  = $this->userRepository->updateEmail($data);
                return $this->responseService->response(
                    ['user' => $user, 'current_user' => $currentUser],
                    null,
                    Response::HTTP_OK
                );
            }
        }
    }

    /**
     * updateRoleAndStatus
     *
     * @param array data
     * @param string uuid
     * @param int id
     *
     * @return array
     */
    public function updateRoleAndStatus(array $data, string $uuid, int $id) : array
    {
        $user = $this->userRepository->findByUuid(base64_decode($uuid));
        if ($user === null) {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_UNAUTHORIZED
            );
        } else {
            $validator = Validator::make($data, RoleAndStatusValidator::$rules);
            if ($validator->fails()) {
                return $this->responseService->response(
                    null,
                    $validator->errors()->toArray(),
                    Response::HTTP_NOT_ACCEPTABLE
                );
            }
            $currentUser = $this->userRepository->findById($id);
            if ($currentUser === null) {
                return $this->responseService->response(
                    ['user' => $user, 'current_user' => $currentUser],
                    null,
                    Response::HTTP_NOT_FOUND
                );
            } else {
                $data['id'] = $currentUser->id;
                $data['email'] = $currentUser->email;
                $data['date'] = Carbon::now();
                $currentUser  = $this->userRepository->updateEmail($data);
                return $this->responseService->response(
                    ['user' => $user, 'current_user' => $currentUser],
                    null,
                    Response::HTTP_OK
                );
            }
        }
    }

    /**
     * destroy
     *
     * @param string uuid
     * @param int id
     *
     * @return array
     */
    public function destroy(string $uuid, int $id) : array
    {
        $user = $this->userRepository->findByUuid(base64_decode($uuid));
        if ($user === null) {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_UNAUTHORIZED
            );
        }
        $user = $this->userRepository->updateExpiredToken([
            'id' => $user->id, 'date' => Carbon::now(),
        ]);
        $currentUser = $this->userRepository->findById($id);
        if ($currentUser == null) {
            return $this->responseService->response(
                ['user' => $user,],
                null,
                Response::HTTP_NOT_FOUND
            );
        } else {
            $this->userRepository->destroy($id);
            return $this->responseService->response(
                ['user' => $user,],
                null,
                Response::HTTP_OK
            );
        }
    }
}
