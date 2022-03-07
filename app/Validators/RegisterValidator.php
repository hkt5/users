<?php

namespace App\Validators;

class RegisterValidator
{
    /**
     * @var mixed rules
     */
    public static $rules = [
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:12|confirmed',
        'password_confirmation' => 'required|string',
    ];
}
