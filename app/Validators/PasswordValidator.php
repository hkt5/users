<?php

namespace App\Validators;

class PasswordValidator
{
    public static $rules = [
        'old_password' => 'required|string',
        'new_password' => 'required|string|min:12',
    ];
}
