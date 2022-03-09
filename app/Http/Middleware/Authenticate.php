<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        if ($request->header('bareer') !== null) {
            $user = $userRepository->findByUuid(
                base64_decode($request->header('bareer'))
            );
            if ($user != null) {
                $authUser = $userRepository->findAuthUser(
                    [
                        'uuid' => base64_decode($request->header('bareer')),
                        'role_id' => $user->role_id,
                        'status_id' => $user->status_id
                    ]
                );
                if ($authUser != null) {
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
