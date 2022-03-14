<?php

namespace App\Http\Middleware;

use App\Enums\RoleId;
use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;

class Authenticate
{

    /**
     * handle
     *
     * @param Request request
     * @param Closure next
     *
     * @return void
     */
    public function handle(Request $request, Closure $next)
    {
        $userRepository = new UserRepository();
        if ($request->header('Baerer') !== null) {
            $user = $userRepository->findByUuid(
                base64_decode($request->header('Baerer'))
            );
            if ($user != null) {
                $authUser = $userRepository->findAuthUser(
                    [
                        'uuid' => base64_decode($request->header('Baerer')),
                        'role_id' => $user->role_id,
                        'status_id' => $user->status_id
                    ]
                );
                if (($authUser != null) && ($authUser->role_id == RoleId::ADMINISTRATOR_ID)) {
                    return $next($request);
                } else {
                    return response('Unauthorized.', 401);
                }
            } else {
                return response('Unauthorized.', 401);
            }
        } else {
            return response('Unauthorized.', 401);
        }
    }
}
