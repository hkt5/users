<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * @var int id
     */
    protected int $id;
    /**
     * @var string uuid
     */
    protected string $uuid;
    /**
     * @var string email
     */
    protected string $email;
    /**
     * @var string password
     */
    protected string $password;
    /**
     * @var int login_attempts
     */
    protected int $login_attempts;
    /**
     * @var bool is_confirmed
     */
    protected bool $is_confirmed;
    /**
     * @var int status_id
     */
    protected int $status_id;
    /**
     * @var int role_id
     */
    protected int $role_id;
    /**
     * @var Carbon expired_token
     */
    protected Carbon $expired_token;

    /**
    * @var Carbon last_password_changed
    */
    protected Carbon $last_password_changed;
    /**
     * @var Carbon created_at
     */
    protected Carbon $created_at;
    /**
     * @var Carbon updated_at
     */
    protected Carbon $updated_at;


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'email', 'password', 'uuid', 'status_id', 'role_id', 'login_attemps',
        'is_confirmed', 'last_password_changed', 'password_expired',
        'expired_token', 'created_at', 'updated_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime', 'updated_at' => 'datetime',
        'last_password_changed' => 'datetime', 'expired_token' => 'datetime',
        'is_confirmed' => 'boolean',
    ];

    /**
     * @var mixed dates
     */
    protected $dates = ['created_at', 'updated_at', 'last_password_changed', 'expired_token',];

    /**
     * role
     *
     * @return BelongsTo
     */
    public function role() : BelongsTo
    {
        return $this->belongsTo('App\Models\Role');
    }

    /**
     * status
     *
     * @return BelongsTo
     */
    public function status() : BelongsTo
    {
        return $this->belongsTo('App\Models\Status');
    }
}
