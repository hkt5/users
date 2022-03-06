<?php

namespace App\Services;

class ResponseService
{

    /**
     * response
     *
     * @param mixed content
     * @param array errors
     * @param int code
     *
     * @return array
     */
    public function response(mixed $content, mixed $errors, int $code) : array
    {
        return [
            'content' => $content, 'errors' => $errors,
            'code' => $code,
        ];
    }
}
