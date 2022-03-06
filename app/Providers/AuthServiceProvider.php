<?php

namespace App\Providers;

use App\Enums\StatusId;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function (Request $request) {
            if ($request->headers->get('Bareer')) {
                $user = User::where('uuid', $request->headers->get('Bareer'))->whereDateBetween(
                    'expired_toke',
                    Carbon::now()->subMinutes(env('TOKEN_EXPIRE'))->toDateString(),
                    Carbon::now()->toDateString()
                )->whereDateBetween(
                        'last_password_changed',
                        Carbon::now()->subDays(env('PASSWORD_EXPIRE '))->startOfDay()->toDateString(),
                        Carbon::now()->endOfDay()->toDateString()
                    )->where('status_id', StatusId::ACTIVE)->first(['*']);
                $user->expired_token = Carbon::now()->addMinutes(env('TOKEN_EXPIRE'));
                $user->updated_at = Carbon::now();
                $user->save();
                return $user;
            }
        });
    }
}
