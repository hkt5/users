<?php

namespace Tests\Repositories;

use App\Enums\RoleId;
use App\Repositories\RoleRepository;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class RoleRepositoryTest extends TestCase
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

    public function test_FindAll_WhenRolesAreAvailable() : void
    {

        // given
        $expectedSize = 4;
        $repository = new RoleRepository();

        // when
        $roles = $repository->findAll();

        // then
        $this->assertEquals($expectedSize, sizeof($roles));
    }

    public function test_FindRole_WhenRoleExists() : void
    {

        // given
        $roleId = (int) RoleId::USER_ID;
        $repository = new RoleRepository();

        // when
        $role = $repository->findById($roleId);

        // then
        $this->assertNotNull($role);
    }

    public function test_FindRole_WhenRoleNotExists() : void
    {

        // given
        $roleId = -1;
        $repository = new RoleRepository();

        // when
        $role = $repository->findById($roleId);

        // then
        $this->assertNull($role);
    }
}
