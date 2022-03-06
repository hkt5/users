<?php

namespace App\Repositories;

use App\Enums\StatusId;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * findAll
     *
     * @return Collection
     */
    public function findAll() : Collection
    {
        return User::all();
    }

    /**
     * findById
     *
     * @param int id
     *
     * @return User
     */
    public function findById(int $id) : ?User
    {
        return User::find($id);
    }

    /**
     * findByEmail
     *
     * @param string email
     *
     * @return User
     */
    public function findByEmail(string $email) : ?User
    {
        return User::where('email', $email)->first(['*']);
    }

    /**
     * findByUuid
     *
     * @param string uuid
     *
     * @return User
     */
    public function findByUuid(string $uuid) : ?User
    {
        return User::where('uuid', $uuid)->first(['*']);
    }

    public function login(array $data) : ?User
    {
        $user = User::where('email', $data['email'])->first(['*']);
        if (
            ($user !== null) && Hash::check($data['password'], $user->password)
            && $user->is_confirmed && ($user->login_attemps < env('LOGIN_ATTEMPS'))
            && ($user->last_password_changed->diffInDays(Carbon::now()) < env('PASSWORD_EXPIRE '))
            && ($user->status->id === StatusId::ACTIVE)
            ) {
            return $user;
        } else {
            $user->login_attemps++;
            $user->updated_at = Carbon::now();
            $user->save();
            return null;
        }
    }

    /**
     * create
     *
     * @param array data
     *
     * @return User
     */
    public function create(array $data) : ?User
    {
        $user = User::create($data);
        return $user;
    }

    /**
     * update
     *
     * @param array data
     *
     * @return User
     */
    public function update(array $data) : ?User
    {
        $user = User::find($data['id']);
        $user->email = $data['email'];
        $user->status_id = $data['status_id'];
        $user->role_id = $data['role_id'];
        $user->uuid = $data['uuid'];
        $user->updated_at = Carbon::now();
        $user->save();
        return $user;
    }

    /**
     * updatePassword
     *
     * @param array data
     *
     * @return User
     */
    public function updatePassword(array $data) : ?User
    {
        $user = User::find($data['id']);
        $user->password = Hash::make($data['password']);
        $user->login_attemps = $data['login_attemps'];
        $user->last_password_changed = $data['date'];
        $user->updated_at = $data['date'];
        $user->save();
        return $user;
    }

    /**
     * updateConfirmation
     *
     * @param array data
     *
     * @return User
     */
    public function updateConfirmation(array $data) : ?User
    {
        $user = User::find($data['id']);
        $user->is_confirmed = true;
        $user->updated_at = $data['date'];
        $user->save();
        return $user;
    }

    /**
     * updateLoginAttemps
     *
     * @param array data
     *
     * @return User
     */
    public function updateLoginAttemps(array $data) : ?User
    {
        $user = User::find($data['id']);
        $user->login_attemps = $data['login_attemps'];
        $user->updated_at = $data['date'];
        $user->save();
        return $user;
    }

    /**
     * updateToken
     *
     * @param array data
     *
     * @return User
     */
    public function updateToken(array $data) : ?User
    {
        $user = User::find($data['id']);
        $user->uuid = $data['uuid'];
        $user->expired_token = $data['expired_token'];
        $user->updated_at = $data['updated_at'];
        $user->save();
        return $user;
    }

    /**
     * destroy
     *
     * @param int id
     *
     * @return void
     */
    public function destroy(int $id) : void
    {
        $user = User::find($id);
        $user->delete();
    }
}