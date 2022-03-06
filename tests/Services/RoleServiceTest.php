<?php

namespace Tests\Services;

use App\Enums\RoleId;
use App\Models\Role;
use App\Repositories\RoleRepository;
use App\Services\ResponseService;
use App\Services\RoleService;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class RoleServiceTest extends TestCase
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

    public function test_FindAllRoles_WhenRolesExists_ThenReturnResponseArray() : void
    {

        // given
        $service = new RoleService(new RoleRepository(), new ResponseService());
        $roles = Role::all()->toArray();
        $expectedResponse = [
            'content' => $roles, 'errors' => null, 'code' => Response::HTTP_OK
        ];

        // when
        $response = $service->findAll();

        // then
        $this->assertEquals($expectedResponse, $response);
    }

    public function test_WhenFindRoleById_ThenRoleExists() : void
    {

        // given
        $id = RoleId::USER_ID;
        $service = new RoleService(new RoleRepository(), new ResponseService());
        $role = Role::find($id);
        $expectedResponse = [
            'content' => $role, 'errors' => null, 'code' => Response::HTTP_OK
        ];

        // when
        $response = $service->findById((int) $id);

        // then
        $this->assertEquals($expectedResponse, $response);
    }

    public function test_WhenFindRoleById_ThenRoleNotExists() : void
    {

        // given
        $id = -1;
        $service = new RoleService(new RoleRepository(), new ResponseService());
        $expectedResponse = [
            'content' => null, 'errors' => null,
            'code' => Response::HTTP_NOT_FOUND
        ];

        // when
        $response = $service->findById((int) $id);

        // then
        $this->assertEquals($expectedResponse, $response);
    }
}
