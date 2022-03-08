<?php

use App\Repositories\UserRepository;
use App\Services\EventService;
use App\Services\ResetPasswordService;
use App\Services\ResponseService;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutEvents;
use Tests\TestCase;

class ResetPasswordServiceTest extends TestCase
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

    public function test_Reset_WhenEmailExists_ThenPasswordResetAndStatusOk() : void
    {

        // given
        $email = 'email@example.com';
        $data = ['email' => $email,];
        $service = new ResetPasswordService(
            new UserRepository(),
            new ResponseService(),
            new EventService()
        );
        $code = Response::HTTP_OK;

        // when
        $result = $service->reset($data);

        // then
        $this->assertEquals($code, $result['code']);
    }

    public function test_Reset_WhenEmailNotExists_ThenStatusIsNotAcceptable() : void
    {

        // given
        $email = 'email3545@example.com';
        $data = ['email' => $email,];
        $service = new ResetPasswordService(
            new UserRepository(),
            new ResponseService(),
            new EventService()
        );
        $code = Response::HTTP_NOT_ACCEPTABLE;
        $expectedResult = [
            'content' => null, 'errors' => null, 'code' => $code,
        ];

        // when
        $result = $service->reset($data);

        // then
        $this->assertEquals($expectedResult, $result);
    }
}
