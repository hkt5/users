<?php

use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutEvents;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use WithoutEvents;
    use WithoutMiddleware;

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

    public function test_FindUSer_WhenUuidExists_ThenReturnUserAndOkStatus() : void
    {
        // given
        $user = User::find(1);
        $code = Response::HTTP_OK;
        // when
        $result = $this->get('/user', ['Bareer' => base64_encode($user->uuid)]);

        // then
        $result->seeStatusCode($code);
    }

    public function test_FindUSer_WhenUuidNotExists_ThenReturnUnathorized() : void
    {
        // given
        $uuid = "uuid";
        $code = Response::HTTP_UNAUTHORIZED;
        $expectedArray = [
            'content' => null, "errors" => null,
        ];

        // when
        $result = $this->get('/user', ['Bareer' => base64_encode($uuid)]);

        // then
        $result->seeStatusCode($code);
        $result->seeJson($expectedArray);
    }

    public function test_UpdateEmail_WhenTokenNotExists_ThenReturnUnauthorized() : void
    {

        // given
        $data = ['email' => 'email23@example.com'];
        $uuid = "uuid";
        $expectedArray = [
            'content' => null, 'errors' => null,
        ];
        $code = Response::HTTP_UNAUTHORIZED;
        // when
        $result = $this->post(
            '/user/update-email',
            $data,
            ['Bareer' => base64_encode($uuid)]
        );

        // then
        $result->seeStatusCode($code);
        $result->seeJson($expectedArray);
    }

    public function test_UpdateEmail_WhenEmailExistsAndAssignedToOtherUser_ThenReturnNotAcceptable() : void
    {

        // given
        $user = User::find(1);
        $data = ['email' => 'email3@example.com'];
        $expectedArray = [
            'content' => null,
            'errors' => [
                'email' => [0 => "The email has already been taken."]
            ],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;
        // when
        $result = $this->post(
            '/user/update-email',
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
        $result->seeJson($expectedArray);
    }

    public function test_UpdateEmail_WhenIsBadFormat_ThenReturnNotAcceptable() : void
    {

        // given
        $user = User::find(1);
        $data = ['email' => 'email3'];
        $expectedArray = [
            'content' => null,
            'errors' => [
                'email' => [0 => "The email must be a valid email address."]
            ],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;
        // when
        $result = $this->post(
            '/user/update-email',
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
        $result->seeJson($expectedArray);
    }

    public function test_UpdateEmail_WhenEmailNotExistst_ThenReturnOK() : void
    {

        // given
        $user = User::find(1);
        $data = ['email' => 'email999@gmail.com'];
        $code = Response::HTTP_OK;

        // when
        $result = $this->post(
            '/user/update-email',
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
    }

    public function test_UpdatePassword_WhenUuidNotExists_ThenReturnUnathorized() : void
    {

        // given
        $data = [
            'old_password' => 'P@ssw0rd',
            'new_password' => 'P@sswordP@ssw0rd',
        ];
        $expctedArray = [
            'content' => null, 'errors' => null,
        ];
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $this->post(
            '/user/update-password',
            $data,
            ['Bareer' => base64_encode("uuid")]
        );

        // then
        $result->seeStatusCode($code);
        $result->seeJson($expctedArray);
    }

    public function test_UpdatePassword_WhenFiledsAreEmpty_ThenReturnNotAcceptable() : void
    {

        // given
        $user = User::find(1);
        $data = [];
        $expctedArray = [
            'content' => null, 'errors' => [
                'old_password' => [0 => 'The old password field is required.'],
                'new_password' => [0 => 'The new password field is required.'],
            ],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $result = $this->post(
            '/user/update-password',
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
        $result->seeJson($expctedArray);
    }

    public function test_UpdatePassword_WhenNewPasswordToShort_ThenReturnNotAcceptable() : void
    {

        // given
        $user = User::find(1);
        $data = [
            'old_password' => 'P@ssw0rd',
            'new_password' => 'P@ssw0rd',
        ];
        $expctedArray = [
            'content' => null, 'errors' => [
                'new_password' => [
                    0 => 'The new password must be at least 12 characters.',
                ],
            ],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $result = $this->post(
            '/user/update-password',
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
        $result->seeJson($expctedArray);
    }

    public function test_UpdatePassword_WhenOldPasswordNotExists_ThenReturnNotAcceptable() : void
    {

        // given
        $user = User::find(1);
        $data = [
            'old_password' => 'P@ssw0rd1',
            'new_password' => 'P@ssw0rdP@ssw0rd',
        ];
        $expctedArray = [
            'content' => null, 'errors' => [
                'old_password' => [0 => 'Old password not exists.'],
            ],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $result = $this->post(
            '/user/update-password',
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
        $result->seeJson($expctedArray);
    }

    public function test_UpdatePassword_WhenDataIsOk_ThenReturnOk() : void
    {

        // given
        $user = User::find(1);
        $data = [
            'old_password' => 'P@ssw0rd',
            'new_password' => 'P@ssw0rdP@ssw0rd',
        ];
        $code = Response::HTTP_OK;

        // when
        $result = $this->post(
            '/user/update-password',
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
    }
}
