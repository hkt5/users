<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminAuthenticate
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
                    $userRepository->updateExpiredToken([
                        'id' => $authUser->id, 'date' => Carbon::now(),
                    ]);
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
