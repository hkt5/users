<?php

namespace Tests\Services;

use App\Services\ResponseService;
use Tests\TestCase;

class ResponseServiceTest extends TestCase
{
    public function test_Response_whenCreateResponse_ThenReturnArray() : void
    {

        // given
        $content = null;
        $errors = null;
        $code = 0;
        $expectedArray = [
            'content' => $content, 'errors' => $errors, 'code' => $code,
        ];
        $service = new ResponseService();

        // when
        $currentArray = $service->response($content, $errors, $code);

        // then
        $this->assertEquals($expectedArray, $currentArray);
    }
}
