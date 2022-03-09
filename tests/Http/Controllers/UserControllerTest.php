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
}
