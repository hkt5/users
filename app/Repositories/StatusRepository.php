<?php

namespace App\Repositories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Collection;

class StatusRepository
{
    /**
     * findAll
     *
     * @return Collection
     */
    public function findAll() : Collection
    {
        return Status::all();
    }

    /**
     * findById
     *
     * @param int id
     *
     * @return Status
     */
    public function findById(int $id) : ?Status
    {
        return Status::find($id);
    }
}
