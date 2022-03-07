<?php

namespace App\Services;

use App\Enums\StatusId;
use App\Repositories\RoleRepository;
use App\Repositories\StatusRepository;
use App\Repositories\UserRepository;
use App\Validators\LoginValidator;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class LoginService
{
    /**
     * @var UserRepository userRepository
     */
    private UserRepository $userRepository;
    /**
     * @var RoleRepository roleRepository
     */
    private RoleRepository $roleRepository;
    /**
     * @var StatusRepository statusRepository
     */
    private StatusRepository $statusRepository;
    /**
     * @var ResponseService service
     */
    private ResponseService $service;


    /**
     * __construct
     *
     * @param UserRepository userRepository
     * @param RoleRepository roleRepository
     * @param StatusRepository statusRepository
     * @param ResponseService service
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        StatusRepository $statusRepository,
        ResponseService $service
    ) {
        $this->userRepository = $userRepository;
        $this->statusRepository = $statusRepository;
        $this->roleRepository = $roleRepository;
        $this->service = $service;
    }

    /**
     * login
     *
     * @param array data
     *
     * @return array
     */
    public function login(array $data) : array
    {
        $validator = Validator::make($data, LoginValidator::$rules);
        if ($validator->fails()) {
            return $this->service->response(
                null,
                $validator->errors()->toArray(),
                Response::HTTP_NOT_ACCEPTABLE
            );
        } else {
            return $this->auth($data);
        }
    }

    /**
     * auth
     *
     * @param array data
     *
     * @return array
     */
    private function auth(array $data) : array
    {
        $user = $this->userRepository->findByEmail($data['email']);
        if ($user == null) {
            return $this->service->response(null, null, Response::HTTP_UNAUTHORIZED);
        } elseif (!$user->is_confirmed) {
            return $this->service->response(
                null,
                ['error' => 'auth.not_confirmed',],
                Response::HTTP_UNAUTHORIZED
            );
        } elseif ($user->login_attemps >= env('LOGIN_ATTEMPS')) {
            $data = $user->toArray();
            $data['status_id'] = StatusId::INACTIVE;
            $user = $this->userRepository->update($data);
            return $this->service->response(
                null,
                ['error' => 'auth.login_attemps',],
                Response::HTTP_UNAUTHORIZED
            );
        } elseif (
            $user->last_password_changed->diffInDays(Carbon::now()) > env('PASSWORD_EXPIRE ')
        ) {
            $data = $user->toArray();
            $data['status_id'] = StatusId::INACTIVE;
            $user = $this->userRepository->update($data);
            return $this->service->response(
                null,
                ['error' => 'auth.password_expired',],
                Response::HTTP_UNAUTHORIZED
            );
        } elseif ($user->status_id === StatusId::INACTIVE) {
            return $this->service->response(
                null,
                ['error' => 'auth.account_inactive',],
                Response::HTTP_UNAUTHORIZED
            );
        } elseif (!Hash::check($data['password'], $user->password)) {
            return $this->service->response(null, null, Response::HTTP_UNAUTHORIZED);
        } else {
            $this->userRepository->updateLoginAttemps(
                [
                    'id' => $user->id,
                    'login_attemps' => 0,
                    'date' => Carbon::now()
                ]
            );
            $currentUser = $this->userRepository->updateToken([
                'id' => $user->id,
                'uuid' => Uuid::uuid4(),
                'expired_token' => Carbon::now()->addMinutes(15),
                'updated_at' => Carbon::now(),
            ]);
            return $this->service->response([
                'user' => $currentUser,
                'role' => $this->roleRepository->findById($currentUser->role_id),
                'status' => $this->statusRepository->findById($currentUser->status_id),
            ], null, Response::HTTP_OK);
        }
    }
}
