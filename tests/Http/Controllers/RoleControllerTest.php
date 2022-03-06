<?php

namespace Tests\Http\Controllers;

use App\Enums\RoleId;
use App\Models\Role;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class RoleControllerTest extends TestCase
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

    public function test_WhenFindAllRoles_ThenReturnAvailableRolesAndStatusOk() : void
    {

        // given
        $roles = Role::all()->toArray();
        $expectedResponse = [
            'content' => $roles, 'errors' => null,
        ];
        $code = Response::HTTP_OK;

        // when
        $response = $this->get('/roles/all');

        // then
        $response->seeStatusCode($code);
        $response->seeJson($expectedResponse);
    }

    public function test_FindRoleById_ThenReturnExistsRoleAndStatusOk() : void
    {

        // given
        $id = RoleId::USER_ID;
        $role = Role::find($id);
        $roleArray = [
            'created_at' => $role->created_at,
            'id' => $role->id,
            'name' => $role->name,
            'updated_at' => $role->updated_at,
        ];
        $expectedResponse = [
            'content' => $roleArray, 'errors' => null,
        ];
        $code = Response::HTTP_OK;

        // when
        $response = $this->get('/roles/by-id/'.$id);

        // then
        $response->seeStatusCode($code);
        $response->seeJson($expectedResponse);
    }

    public function test_WhenFindRoleById_ThenReturnNotExistsRoleAndStatusNotFound() : void
    {

        // given
        $id = -1;
        $expectedResponse = [
            'content' => null, 'errors' => null,
        ];
        $code = Response::HTTP_NOT_FOUND;

        // when
        $response = $this->get('/roles/by-id/'.$id);

        // then
        $response->seeStatusCode($code);
        $response->seeJson($expectedResponse);
    }
}
