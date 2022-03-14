<?php

namespace App\Validators;

class NewUserValidator
{
    public static $rules = [
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:12|confirmed',
        'password_confirmation' => 'required|string',
        'role_id' => 'required|int|exists:roles,id',
        'status_id' => 'required|int|exists:statuses,id',
    ];
}
