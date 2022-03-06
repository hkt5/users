<?php

namespace Tests\Services;

use App\Enums\StatusId;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\LoginService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LoginServiceTest extends TestCase
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

    public function test_Login_WhenEmailAndPasswordFieldsAreEmpty_ThenResponseIsNotAcceptable() : void
    {

        // given
        $data = [];
        $expectedArray = [
            'content' => null, 'errors' => [
                'email' => [
                    0 => 'The email field is required.'
                ], 'password' => [
                    0 => 'The password field is required.'
                ],
            ],
            'code' => Response::HTTP_NOT_ACCEPTABLE,
        ];
        $service = new LoginService(new UserRepository(), new ResponseService());

        // when
        $response = $service->login($data);

        // then
        $this->assertEquals($expectedArray, $response);
    }

    public function test_Login_WhenEmailNotExists_ThenResponseIsUnathorized() : void
    {

        // given
        $data = [
            'email' => 'notExistsEmail@example.com',
            'password' => 'P@ssword',
        ];
        $expectedArray = [
            'content' => null, 'errors' => null, 'code' => Response::HTTP_UNAUTHORIZED
        ];
        $service = new LoginService(new UserRepository(), new ResponseService());

        // when
        $response = $service->login($data);

        // then
        $this->assertEquals($expectedArray, $response);
    }

    public function test_Login_WhenUserIsNotConfirmed_ThenReturnUnauthorized() : void
    {

        // given
        $email = 'email@example.com';
        $user = User::where('email', $email)->first(['*']);
        $user->is_confirmed = false;
        $user->save();
        $data = [
            'email' => $email,
            'password' => 'P@ssw0rd',
        ];
        $expectedArray = [
            'content' => null,
            'errors' => ['error' => 'auth.not_confirmed',],
            'code' => Response::HTTP_UNAUTHORIZED
        ];
        $service = new LoginService(new UserRepository(), new ResponseService());

        // when
        $response = $service->login($data);

        // then
        $this->assertEquals($expectedArray, $response);
    }

    public function test_Login_WhenLoginAttempsIsPresent_ThenReturnUnathorized() : void
    {

        // given
        $email = 'email@example.com';
        $user = User::where('email', $email)->first(['*']);
        $user->login_attemps = 3;
        $user->save();
        $data = [
            'email' => $email,
            'password' => 'P@ssw0rd',
        ];
        $expectedArray = [
            'content' => null,
            'errors' => ['error' => 'auth.login_attemps',],
            'code' => Response::HTTP_UNAUTHORIZED
        ];
        $service = new LoginService(new UserRepository(), new ResponseService());

        // when
        $response = $service->login($data);

        // then
        $this->assertEquals($expectedArray, $response);
    }

    public function test_Login_WhenPasswordExpired_ThenReturnUnathorized() : void
    {

        // given
        $email = 'email@example.com';
        $user = User::where('email', $email)->first(['*']);
        $user->last_password_changed = Carbon::now()->subDays(31);
        $user->save();
        $data = [
            'email' => $email,
            'password' => 'P@ssw0rd',
        ];
        $expectedArray = [
            'content' => null,
            'errors' => ['error' => 'auth.password_expired',],
            'code' => Response::HTTP_UNAUTHORIZED
        ];
        $service = new LoginService(new UserRepository(), new ResponseService());

        // when
        $response = $service->login($data);

        // then
        $this->assertEquals($expectedArray, $response);
    }

    public function test_Login_WhenAccountIsInactive_ThenReturnUnathorized() : void
    {

        // given
        $email = 'email@example.com';
        $user = User::where('email', $email)->first(['*']);
        $user->status_id = StatusId::INACTIVE;
        $user->save();
        $data = [
            'email' => $email,
            'password' => 'P@ssw0rd',
        ];
        $expectedArray = [
            'content' => null,
            'errors' => ['error' => 'auth.account_inactive',],
            'code' => Response::HTTP_UNAUTHORIZED
        ];
        $service = new LoginService(new UserRepository(), new ResponseService());

        // when
        $response = $service->login($data);

        // then
        $this->assertEquals($expectedArray, $response);
    }

    public function test_Login_WhenPasswordIsIncorrect_ThenReturnUnathorized() : void
    {

        // given
        $data = [
            'email' => 'email@example.com',
            'password' => 'P@ssw0rd1',
        ];
        $expectedArray = [
            'content' => null, 'errors' => null, 'code' => Response::HTTP_UNAUTHORIZED
        ];
        $service = new LoginService(new UserRepository(), new ResponseService());

        // when
        $response = $service->login($data);

        // then
        $this->assertEquals($expectedArray, $response);
    }

    public function test_Login_WhenCredentialsAreExists_ThenReturnOK() : void
    {

        // given
        $email = 'email@example.com';
        $data = [
            'email' => $email,
            'password' => 'P@ssw0rd',
        ];
        $service = new LoginService(new UserRepository(), new ResponseService());

        // when
        $response = $service->login($data);

        // then
        $this->assertEquals(null, $response['errors']);
        $this->assertNotNull($response['content']);
        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}
