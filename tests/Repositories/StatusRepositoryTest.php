<?php

namespace Tests\Repositories;

use App\Enums\StatusId;
use App\Repositories\StatusRepository;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class StatusRepositoryTest extends TestCase
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

    public function test_FindAll_WhenStatusesAreAvailable() : void
    {

        // given
        $expectedSize = 2;
        $repository = new StatusRepository();

        // when
        $statuses = $repository->findAll();

        // then
        $this->assertEquals($expectedSize, sizeof($statuses));
    }

    public function test_FindRole_WhenStatusExists() : void
    {

        // given
        $statusId = (int) StatusId::ACTIVE;
        $repository = new StatusRepository();

        // when
        $status = $repository->findById($statusId);

        // then
        $this->assertNotNull($status);
    }

    public function test_FindRole_WhenStatusNotExists() : void
    {

        // given
        $statusId = -1;
        $repository = new StatusRepository();

        // when
        $status = $repository->findById($statusId);

        // then
        $this->assertNull($status);
    }
}
