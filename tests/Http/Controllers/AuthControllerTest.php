<?php

use App\Enums\StatusId;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutEvents;
use Tests\TestCase;

class AuthControllerTest extends TestCase
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
        ];
        $status = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $response = $this->post('/auth/login', $data);

        // then
        $response->seeJson($expectedArray);
        $response->seeStatusCode($status);
    }

    public function test_Login_WhenEmailNotExists_ThenResponseIsUnathorized() : void
    {

        // given
        $data = [
            'email' => 'notExistsEmail@example.com',
            'password' => 'P@ssword',
        ];
        $expectedArray = ['content' => null, 'errors' => null,];
        $status = Response::HTTP_UNAUTHORIZED;

        // when
        $response = $this->post('/auth/login', $data);

        // then
        $response->seeJson($expectedArray);
        $response->seeStatusCode($status);
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
        ];
        $status = Response::HTTP_UNAUTHORIZED;

        // when
        $response = $this->post('/auth/login', $data);

        // then
        $response->seeJson($expectedArray);
        $response->seeStatusCode($status);
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
        ];
        $status = Response::HTTP_UNAUTHORIZED;

        // when
        $response = $this->post('/auth/login', $data);

        // then
        $response->seeJson($expectedArray);
        $response->seeStatusCode($status);
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
        ];
        $status = Response::HTTP_UNAUTHORIZED;

        // when
        $response = $this->post('/auth/login', $data);

        // then
        $response->seeJson($expectedArray);
        $response->seeStatusCode($status);
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
        ];
        $status = Response::HTTP_UNAUTHORIZED;

        // when
        $response = $this->post('/auth/login', $data);

        // then
        $response->seeJson($expectedArray);
        $response->seeStatusCode($status);
    }

    public function test_Login_WhenPasswordIsIncorrect_ThenReturnUnathorized() : void
    {

        // given
        $data = [
            'email' => 'email@example.com',
            'password' => 'P@ssw0rd1',
        ];
        $expectedArray = [
            'content' => null, 'errors' => null,
        ];
        $status = Response::HTTP_UNAUTHORIZED;

        // when
        $response = $this->post('/auth/login', $data);

        // then
        $response->seeJson($expectedArray);
        $response->seeStatusCode($status);
    }

    public function test_Login_WhenCredentialsAreExists_ThenReturnOK() : void
    {

        // given
        $email = 'email@example.com';
        $data = [
            'email' => $email,
            'password' => 'P@ssw0rd',
        ];
        $contains = ['errors' => null,];
        $status = Response::HTTP_OK;

        // when
        $response = $this->post('/auth/login', $data);

        // then
        $response->seeJsonContains($contains);
        $response->seeStatusCode($status);
    }
}