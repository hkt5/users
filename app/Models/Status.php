<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Status extends Model
{

    /**
     * @var int id
     */
    protected int $id;
    /**
     * @var string name
     */
    protected string $name;
    /**
     * @var Carbon created_at
     */
    protected Carbon $created_at;
    /**
     * @var Carbon updated_at
     */
    protected Carbon $updated_at;
    /**
     * @var mixed table
     */
    protected $table = 'statuses';
    /**
     * @var mixed primaryKey
     */
    /**
     * @var mixed primaryKey
     */
    protected $primaryKey = 'id';
    /**
     * @var mixed fillable
     */
    protected $fillable = ['name', 'created_at', 'updated_at'];
    /**
     * @var mixed casts
     */
    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime',];
    /**
     * @var mixed dates
     */
    protected $dates = ['created_at', 'updated_at',];
}