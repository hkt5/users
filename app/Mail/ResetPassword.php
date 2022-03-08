<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class ResetPassword extends Mailable
{
    use Queueable;

    private array $data;

    /**
     * __construct
     *
     * @param array data
     *
     * @return void
     */
    public function __construct(
        array $data
    ) {
        $this->data = $data;
    }

    /**
     * build
     *
     * @return void
     */
    public function build() : RegisterEmail
    {
        return $this->view('emails.password')->with('data', $this->data);
    }
}
