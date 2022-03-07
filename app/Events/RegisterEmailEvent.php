<?php

namespace App\Events;

use Illuminate\Http\Request;

class RegisterEmailEvent extends Event
{
    /**
     * @var array data
     */
    public array $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
