<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;

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
     * @var EventService eventService
     */
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

    /**
     * regenerateToken
     *
     * @param array data
     *
     * @return array
     */
    public function regenerateToken(array $data) : array
    {
        $currentUser = $this->userRepository->findByUuid($data['uuid']);
        if ($currentUser != null) {
            return $this->generateNewToken($currentUser);
        } else {
            return $this->responseService->response(
                null,
                null,
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * generateNewToken
     *
     * @param User currentUser
     *
     * @return array
     */
    private function generateNewToken(User $currentUser) : ?array
    {
        $user = $this->userRepository->updateToken([
            'id' => $currentUser->id, 'uuid' => Uuid::uuid4(),
            'expired_token' => Carbon::now()->addMinutes(env('TOKEN_EXPIRE')),
            'updated_at' => Carbon::now()
        ]);
        if ($user != null) {
            $this->eventService->sendRegisterEmail($user->toArray());
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
