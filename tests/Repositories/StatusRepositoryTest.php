<?php

namespace Tests\Repositories;

use App\Enums\StatusId;
use App\Repositories\StatusRepository;
use Tests\TestCase;

class StatusRepositoryTest extends TestCase
{
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
