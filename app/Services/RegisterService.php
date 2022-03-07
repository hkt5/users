<?php

namespace App\Services;

use App\Enums\RoleId;
use App\Enums\StatusId;
use App\Repositories\UserRepository;
use App\Validators\RegisterValidator;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class RegisterService
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
     * @var EventService eventService
     */
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

    /**
     * registryNewUser
     *
     * @param array data
     *
     * @return array
     */
    public function registryNewUser(array $data) : array
    {
        $validator = Validator::make($data, RegisterValidator::$rules);
        if ($validator->fails()) {
            $resposne = $this->responseService->response(
                null,
                $validator->errors()->toArray(),
                Response::HTTP_NOT_ACCEPTABLE
            );
            return $resposne;
        } else {
            $data = [
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'uuid' => Uuid::uuid4(),
                'status_id' => StatusId::INACTIVE,
                'role_id' => RoleId::USER_ID,
                'login_attempts' => 0,
                'is_confiormed' => false,
                'last_password_changed' => Carbon::now(),
                'expired_token' => Carbon::now()->addMinutes(env('TOKEN_EXPIRE')),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $user = $this->userRepository->create($data);
            $this->eventService->sendRegisterEmail($user->toArray());
            return $this->responseService->response(['user' => $user], null, Response::HTTP_OK);
        }
    }
}
