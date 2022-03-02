<?php

namespace Tests\Http\Controllers;

use App\Enums\StatusId;
use App\Models\Status;
use Illuminate\Http\Response;
use Tests\TestCase;

class StatusControllerTest extends TestCase
{
    public function test_WhenFindAllStatuses_ThenReturnAvailableStatusesAndStatusOk() : void
    {

        // given
        $roles = Status::all()->toArray();
        $expectedResponse = [
            'content' => $roles, 'errors' => null,
        ];
        $code = Response::HTTP_OK;

        // when
        $response = $this->get('/statuses/all');

        // then
        $response->seeStatusCode($code);
        $response->seeJson($expectedResponse);
    }

    public function test_FindStatusById_ThenReturnExistsStatusAndStatusOk() : void
    {

        // given
        $id = StatusId::ACTIVE;
        $status = Status::find($id);
        $statusArray = [
            'created_at' => $status->created_at,
            'id' => $status->id,
            'name' => $status->name,
            'updated_at' => $status->updated_at,
        ];
        $expectedResponse = [
            'content' => $statusArray, 'errors' => null,
        ];
        $code = Response::HTTP_OK;

        // when
        $response = $this->get('/statuses/by-id/'.$id);

        // then
        $response->seeStatusCode($code);
        $response->seeJson($expectedResponse);
    }

    public function test_WhenFindStatusById_ThenReturnNotExistsStatusAndStatusNotFound() : void
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
