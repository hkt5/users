<?php

namespace App\Validators;

class EmailValidator
{
    public static $rules = [
        'email' => 'required|email|unique:users,email',
    ];
}
