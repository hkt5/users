<?php

namespace Tests\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\EventService;
use App\Services\PasswordConfirmationService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutEvents;
use Tests\TestCase;

class PasswordConfirmationServiceTest extends TestCase
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

    public function test_ConfirmPassword_WhenDataIsOk_ThenReturnUser() : void
    {

        // given
        $id = 1;
        $user = User::find($id);
        $user->expired_token = Carbon::now()->addMinutes(env('TOKEN_EXPIRE'));
        $user->is_confirmed = false;
        $user->save();
        $code = Response::HTTP_OK;
        $service = new PasswordConfirmationService(
            new UserRepository(),
            new ResponseService(),
            new EventService()
        );

        // when
        $result = $service->confirm(['uuid' => $user->uuid]);

        // then
        $this->assertNotNull($result['content']['user']);
        $this->assertEquals($code, $result['code']);
    }

    public function test_ConfirmPassword_WhenTokenExpired_ThenReturnNull() : void
    {

        // given
        $id = 1;
        $user = User::find($id);
        $user->expired_token = Carbon::now()->subMinutes(env('TOKEN_EXPIRE') + 1);
        $user->is_confirmed = false;
        $user->save();
        $data = [
            'content' => null, 'errors' => null, 'code' => Response::HTTP_NOT_ACCEPTABLE,
        ];
        $service = new PasswordConfirmationService(
            new UserRepository(),
            new ResponseService(),
            new EventService()
        );

        // when
        $result = $service->confirm(['uuid' => $user->uuid]);

        // then
        $this->assertEquals($data, $result);
    }

    public function test_ConfirmPassword_WhenAccountIsConfirmed_ThenReturnNull() : void
    {

        // given
        $id = 1;
        $user = User::find($id);
        $user->expired_token = Carbon::now()->addMinutes(env('TOKEN_EXPIRE'));
        $user->is_confirmed = true;
        $user->save();
        $data = [
            'content' => null, 'errors' => null, 'code' => Response::HTTP_NOT_ACCEPTABLE,
        ];
        $service = new PasswordConfirmationService(
            new UserRepository(),
            new ResponseService(),
            new EventService()
        );

        // when
        $result = $service->confirm(['uuid' => $user->uuid]);

        // then
        $this->assertEquals($data, $result);
    }

    public function test_ConfirmPassword_WhenUuidNotExists_ThenReturnNull() : void
    {

        // given
        $data = [
            'content' => null, 'errors' => null, 'code' => Response::HTTP_NOT_ACCEPTABLE,
        ];
        $service = new PasswordConfirmationService(
            new UserRepository(),
            new ResponseService(),
            new EventService()
        );

        // when
        $result = $service->confirm(['uuid' => "uuid"]);

        // then
        $this->assertEquals($data, $result);
    }

    public function test_GenerateToken_WhenOldTokenExists_ReturnStatusOk() : void
    {

        // given
        $id = 1;
        $user = User::find($id);

        $data = [
            'content' => null, 'errors' => null, 'code' => Response::HTTP_OK,
        ];
        $service = new PasswordConfirmationService(
            new UserRepository(),
            new ResponseService(),
            new EventService()
        );

        // when
        $result = $service->regenerateToken(['uuid' => $user->uuid]);

        // then
        $this->assertEquals($data['code'], $result['code']);
        $this->assertEquals($data['errors'], $result['errors']);
    }

    public function test_GenerateToken_WhenOldTokenNotExists_ReturnStatusNotFound() : void
    {

        // given
        $data = [
            'content' => null, 'errors' => null, 'code' => Response::HTTP_NOT_FOUND,
        ];
        $service = new PasswordConfirmationService(
            new UserRepository(),
            new ResponseService(),
            new EventService()
        );

        // when
        $result = $service->regenerateToken(['uuid' => "uuid"]);

        // then
        $this->assertEquals($data['code'], $result['code']);
        $this->assertEquals($data['errors'], $result['errors']);
    }
}
