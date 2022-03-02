<?php

namespace Tests\Services;

use App\Enums\StatusId;
use App\Models\Status;
use App\Repositories\StatusRepository;
use App\Services\StatusService;
use Illuminate\Http\Response;
use Tests\TestCase;

class StatusServiceTest extends TestCase
{
    public function test_FindAllRoles_WhenStatusesExists_ThenReturnResponseArray() : void
    {

        // given
        $service = new StatusService(new StatusRepository());
        $statuses = Status::all()->toArray();
        $expectedResponse = [
            'content' => $statuses, 'errors' => null, 'code' => Response::HTTP_OK
        ];

        // when
        $response = $service->findAll();

        // then
        $this->assertEquals($expectedResponse, $response);
    }

    public function test_WhenFindStatusById_ThenStatusExists() : void
    {

        // given
        $id = StatusId::ACTIVE;
        $service = new StatusService(new StatusRepository());
        $status = Status::find($id);
        $expectedResponse = [
            'content' => $status, 'errors' => null, 'code' => Response::HTTP_OK
        ];

        // when
        $response = $service->findById((int) $id);

        // then
        $this->assertEquals($expectedResponse, $response);
    }

    public function test_WhenFindStatusById_ThenStatusNotExists() : void
    {

        // given
        $id = -1;
        $service = new StatusService(new StatusRepository());
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
