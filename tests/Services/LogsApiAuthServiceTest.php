<?php

namespace Tests\Services;

use App\Enums\RoleId;
use App\Enums\StatusId;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\LogsApiAuthService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LogsApiAuthServiceTest extends TestCase
{
    use DatabaseMigrations {
        runDatabaseMigrations as baseRunDatabaseMigrations;
    }

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        $this->baseRunDatabaseMigrations();
        $this->artisan('db:seed');
    }

    public function test_Auth_WhenUserIsAdmin_ThenStatusIsOk(): void
    {

        // given
        $id = 1;
        $date = Carbon::now();
        $user = User::find($id);
        $user->expired_token = $date;
        $user->last_password_changed = $date;
        $user->status_id = StatusId::ACTIVE;
        $user->role_id = RoleId::ADMINISTRATOR_ID;
        $user->save();
        $token = base64_encode($user->uuid);
        $service = new LogsApiAuthService(new UserRepository(), new ResponseService());
        $expectedArray = [
            'content' => null, 'errors' => null, 'code' => Response::HTTP_OK,
        ];

        // when
        $result = $service->auth($token);

        // then
        $this->assertEquals($expectedArray, $result);
    }

    public function test_Auth_WhenUserIsAdmin_ThenStatusIsUnathorized(): void
    {

        // given
        $id = 1;
        $date = Carbon::now();
        $user = User::find($id);
        $user->expired_token = $date;
        $user->last_password_changed = $date;
        $user->status_id = StatusId::ACTIVE;
        $user->role_id = RoleId::USER_ID;
        $user->save();
        $token = base64_encode($user->uuid);
        $service = new LogsApiAuthService(new UserRepository(), new ResponseService());
        $expectedArray = [
            'content' => null, 'errors' => null, 'code' => Response::HTTP_UNAUTHORIZED,
        ];

        // when
        $result = $service->auth($token);

        // then
        $this->assertEquals($expectedArray, $result);
    }
}
