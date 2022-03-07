<?php

namespace Tests\Services;

use App\Repositories\UserRepository;
use App\Services\EventService;
use App\Services\RegisterService;
use App\Services\ResponseService;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutEvents;
use Tests\TestCase;

class RegisterServiceTest extends TestCase
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

    public function test_RegistryUser_WhenFieldsAreEmpty_ThenReturnNotAcceptableCode() : void
    {

        // given
        $data = [];
        $expectedArray = [
            'content' => null,
            'errors' => [
                'email' => [0 => 'The email field is required.'],
                'password' => [0 => 'The password field is required.'],
                'password_confirmation' => [0 => 'The password confirmation field is required.'],
            ],
            'code' => Response::HTTP_NOT_ACCEPTABLE
        ];
        $service = new RegisterService(new UserRepository(), new ResponseService(), new EventService());

        // when
        $result = $service->registryNewUser($data);

        // then
        $this->assertEquals($expectedArray, $result);
    }

    public function test_RegistryUser_WhenEmailHasBadFormat_ThenReturnNotAcceptableCode() : void
    {

        // given
        $data = [
            'email' => 'email',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssw0rd'
        ];
        $expectedArray = [
            'content' => null,
            'errors' => [
                'email' => [0 => 'The email must be a valid email address.'],
            ],
            'code' => Response::HTTP_NOT_ACCEPTABLE
        ];
        $service = new RegisterService(new UserRepository(), new ResponseService(), new EventService());

        // when
        $result = $service->registryNewUser($data);

        // then
        $this->assertEquals($expectedArray, $result);
    }

    public function test_RegistryUser_WhenEmailExists_ThenReturnNotAcceptableCode() : void
    {

        // given
        $data = [
            'email' => 'email@example.com',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssw0rd'
        ];
        $expectedArray = [
            'content' => null,
            'errors' => [
                'email' => [0 => 'The email has already been taken.'],
            ],
            'code' => Response::HTTP_NOT_ACCEPTABLE
        ];
        $service = new RegisterService(new UserRepository(), new ResponseService(), new EventService());

        // when
        $result = $service->registryNewUser($data);

        // then
        $this->assertEquals($expectedArray, $result);
    }

    public function test_RegistryUser_WhenPasswordToShort_ThenReturnNotAcceptableCode() : void
    {

        // given
        $data = [
            'email' => 'email23@example.com',
            'password' => 'P@ssw0rd',
            'password_confirmation' => 'P@ssw0rd'
        ];
        $expectedArray = [
            'content' => null,
            'errors' => [
                'password' => [0 => 'The password must be at least 12 characters.'],
            ],
            'code' => Response::HTTP_NOT_ACCEPTABLE
        ];
        $service = new RegisterService(new UserRepository(), new ResponseService(), new EventService());

        // when
        $result = $service->registryNewUser($data);

        // then
        $this->assertEquals($expectedArray, $result);
    }

    public function test_RegistryUser_WhenPasswordsMismatch_ThenReturnNotAcceptableCode() : void
    {

        // given
        $data = [
            'email' => 'email23@example.com',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssw0rdP@ssw0rd'
        ];
        $expectedArray = [
            'content' => null,
            'errors' => [
                'password' => [0 => 'The password confirmation does not match.'],
            ],
            'code' => Response::HTTP_NOT_ACCEPTABLE
        ];
        $service = new RegisterService(new UserRepository(), new ResponseService(), new EventService());

        // when
        $result = $service->registryNewUser($data);

        // then
        $this->assertEquals($expectedArray, $result);
    }

    public function test_RegistryUser_WhenUserDataIsOk_ThenReturnOKCode() : void
    {

        // given
        $data = [
            'email' => 'email23@example.com',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssw0rd'
        ];
        $expectedArray = [
            'errors' => null,
            'code' => Response::HTTP_OK
        ];
        $service = new RegisterService(new UserRepository(), new ResponseService(), new EventService());

        // when
        $result = $service->registryNewUser($data);

        // then
        $this->assertEquals($expectedArray['code'], $result['code']);
        $this->assertEquals($expectedArray['errors'], $result['errors']);
    }
}
