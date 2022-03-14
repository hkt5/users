<?php

namespace App\Validators;

class RoleAndStatusValidator
{
    public static $rules = [
        'role_id' => 'required|int|exists:roles,id',
        'status_id' => 'required|int|exists:statuses,id'
    ];
}
