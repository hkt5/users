<?php

namespace App\Validators;

class LoginValidator
{
    public static $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];
}
