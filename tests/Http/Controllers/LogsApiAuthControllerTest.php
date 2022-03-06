<?php

namespace Tests\Http\Controllers;

use App\Enums\RoleId;
use App\Enums\StatusId;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutEvents;
use Tests\TestCase;

class LogsApiAuthControllerTest extends TestCase
{
    use WithoutEvents;

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
        $expectedArray = [
            'content' => null, 'errors' => null,
        ];
        $status = Response::HTTP_OK;

        // when
        $result = $this->post('/api/logs', [], ['Bareer' => $token]);

        // then
        $this->seeStatusCode($status);
        $this->seeJsonContains($expectedArray);
    }

    public function test_Auth_WhenUserIsNotAdmin_ThenStatusIsUnathorized(): void
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
        $expectedArray = [
            'content' => null, 'errors' => null,
        ];
        $status = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $this->post('/api/logs', [], ['Bareer' => $token]);

        // then
        $result->seeJsonContains($expectedArray);
        $result->seeStatusCode($status);
    }
}
