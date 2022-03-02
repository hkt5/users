<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    /**
     * findAll
     *
     * @return Collection
     */
    public function findAll() : Collection
    {
        return Role::all();
    }

    /**
     * findById
     *
     * @param int id
     *
     * @return Role
     */
    public function findById(int $id) : ?Role
    {
        return Role::find($id);
    }
}
