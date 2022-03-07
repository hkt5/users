<?php

namespace Tests\Repositories;

use App\Enums\RoleId;
use App\Enums\StatusId;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use DatabaseMigrations {
        runDatabaseMigrations as baseRunDatabaseMigrations;
    }

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        $this->baseRunDatabaseMigrations();
        $this->artisan('db:seed');
    }

    public function test_FindAll_WhenFindAllUsers_ThenReturnThreeDefaultUsers() : void
    {

        // given
        $repository = new UserRepository();
        $expectedSize = 3;

        // when
        $resultSize = sizeof($repository->findAll());

        // then
        $this->assertEquals($expectedSize, $resultSize);
    }

    public function test_FindById_WhenFindUserByExistsId_ThenReturnUser() : void
    {

        // given
        $id = 1;
        $repository = new UserRepository();
        $expectedUser = User::find($id);

        // when
        $currentUser = $repository->findById($id);

        // then
        $this->assertEquals($expectedUser, $currentUser);
    }

    public function test_FindById_WhenFindUserByNotExistsId_ThenReturnNull() : void
    {

        // given
        $id = -1;
        $repository = new UserRepository();
        $expectedUser = null;

        // when
        $currentUser = $repository->findById($id);

        // then
        $this->assertEquals($expectedUser, $currentUser);
    }

    public function test_FindByEmail_WhenEmailExists_ThenReturnUser() : void
    {

        // given
        $email = "email@example.com";
        $expectedUser = User::where('email', $email)->first(['*']);
        $repository = new UserRepository();

        // when
        $currentUser = $repository->findByEmail($email);

        // then
        $this->assertEquals($expectedUser, $currentUser);
    }

    public function test_FindByEmail_WhenEmailNotExists_ThenReturnNull() : void
    {

        // given
        $email = "email111111@example.com";
        $expectedUser = null;
        $repository = new UserRepository();

        // when
        $currentUser = $repository->findByEmail($email);

        // then
        $this->assertEquals($expectedUser, $currentUser);
    }

    public function test_FindByUuid_WhenUuidExists_ThenUserExists() : void
    {

        // given
        $id = 1;
        $expectedUser = User::find($id);
        $repository = new UserRepository();

        // when
        $currentUser = $repository->findByUuid($expectedUser->uuid);

        // then
        $this->assertEquals($expectedUser, $currentUser);
    }

    public function test_FindByUuid_WhenUuidNotExists_ThenUserNotExists() : void
    {

        // given
        $uuid = Uuid::uuid4();
        $expectedUser = null;
        $repository = new UserRepository();

        // when
        $currentUser = $repository->findByUuid($uuid);

        // then
        $this->assertEquals($expectedUser, $currentUser);
    }


    public function test_FindAuthUser_WhenUuidExists_ThenReturnUser() : void
    {

        // given
        $id = 1;
        $date = Carbon::now();
        $user = User::find($id);
        $user->expired_token = $date;
        $user->last_password_changed = $date;
        $user->status_id = StatusId::ACTIVE;
        $user->save();
        $repository = new UserRepository();

        // when
        $result = $repository->findAuthUser(
            [
                'uuid' => $user->uuid, 'status_id' => $user->status_id,
                'role_id' => $user->role_id,
            ]
        );

        // then
        $this->assertNotNull($result);
    }

    public function test_FindAuthUser_WhenUuidNotExists_ThenReturnNull() : void
    {

        // given
        $id = 1;
        $date = Carbon::now();
        $user = User::find($id);
        $user->expired_token = $date;
        $user->last_password_changed = $date;
        $user->status_id = StatusId::ACTIVE;
        $user->save();
        $repository = new UserRepository();

        // when
        $result = $repository->findAuthUser(
            [
                'uuid' => Uuid::uuid4(), 'status_id' => $user->status_id,
                'role_id' => $user->role_id,
            ]
        );

        // then
        $this->assertNull($result);
    }

    public function test_FindAuthUser_WhenTokenExpired_ThenReturnNull() : void
    {

        // given
        $id = 1;
        $date = Carbon::now();
        $user = User::find($id);
        $user->expired_token = $date->subDay();
        $user->last_password_changed = $date;
        $user->status_id = StatusId::ACTIVE;
        $user->save();
        $repository = new UserRepository();

        // when
        $result = $repository->findAuthUser(
            [
                'uuid' => $user->uuid, 'status_id' => $user->status_id,
                'role_id' => $user->role_id,
            ]
        );

        // then
        $this->assertNull($result);
    }

    public function test_FindAuthUser_WhenPasswordExpired_ThenReturnNull() : void
    {

        // given
        $id = 1;
        $date = Carbon::now();
        $user = User::find($id);
        $user->expired_token = $date;
        $user->last_password_changed = $date->subDays(31);
        $user->status_id = StatusId::ACTIVE;
        $user->save();
        $repository = new UserRepository();

        // when
        $result = $repository->findAuthUser(
            [
                'uuid' => $user->uuid, 'status_id' => $user->status_id,
                'role_id' => $user->role_id,
            ]
        );

        // then
        $this->assertNull($result);
    }

    public function test_FindAuthUser_WhenAccountNotConfirmed_ThenReturnNull() : void
    {

        // given
        $id = 1;
        $date = Carbon::now();
        $user = User::find($id);
        $user->expired_token = $date;
        $user->last_password_changed = $date;
        $user->is_confirmed = false;
        $user->status_id = StatusId::ACTIVE;
        $user->save();
        $repository = new UserRepository();

        // when
        $result = $repository->findAuthUser(
            [
                'uuid' => $user->uuid, 'status_id' => $user->status_id,
                'role_id' => $user->role_id,
            ]
        );

        // then
        $this->assertNull($result);
    }

    public function test_FindAuthUser_WhenStatusNotExists_ThenReturnNull() : void
    {

        // given
        $id = 1;
        $date = Carbon::now();
        $user = User::find($id);
        $user->expired_token = $date;
        $user->last_password_changed = $date;
        $user->status_id = StatusId::ACTIVE;
        $user->save();
        $repository = new UserRepository();

        // when
        $result = $repository->findAuthUser(
            [
                'uuid' => $user->uuid, 'status_id' => StatusId::INACTIVE,
                'role_id' => $user->role_id,
            ]
        );

        // then
        $this->assertNull($result);
    }

    public function test_FindAuthUser_WhenRoleNotExists_ThenReturnNull() : void
    {

        // given
        $id = 1;
        $date = Carbon::now();
        $user = User::find($id);
        $user->expired_token = $date;
        $user->last_password_changed = $date;
        $user->status_id = StatusId::ACTIVE;
        $user->save();
        $repository = new UserRepository();

        // when
        $result = $repository->findAuthUser(
            [
                'uuid' => $user->uuid, 'status_id' => $user->status_id,
                'role_id' => 15,
            ]
        );

        // then
        $this->assertNull($result);
    }

    public function test_Create() : void
    {

        // given
        $repository = new UserRepository();
        $currentDate = Carbon::now();
        $uuid = Uuid::uuid4();
        $email = 'newemail@example.com';
        $data = [
            'email' => $email,
            'password' => 'P@ssw0rd',
            'uuid' => $uuid,
            'status_id' => StatusId::ACTIVE,
            'role_id' => RoleId::USER_ID,
            'login_attemps' => 0,
            'is_confirmed' => false,
            'last_password_changed' => $currentDate,
            'created_at' => $currentDate,
            'updated_at' => $currentDate,
        ];

        // when
        $user = $repository->create($data);

        // then
        $this->assertNotNull($user);
        $this->seeInDatabase('users', ['email' => $email]);
        $this->seeInDatabase('users', ['uuid' => $uuid]);
        $this->seeInDatabase('users', ['last_password_changed' => $currentDate]);
        $this->seeInDatabase('users', ['created_at' => $currentDate]);
        $this->seeInDatabase('users', ['updated_at' => $currentDate]);
    }

    public function test_WhenUserExists_ThenUpdateUserData() : void
    {

        // given
        $repository = new UserRepository();
        $id = 1;
        $email = 'email99999@example.com';
        $uuid = Uuid::uuid4();
        $updatedAt = Carbon::now();
        $data = [
            'id' => $id,
            'email' => $email,
            'status_id' => StatusId::ACTIVE,
            'role_id' => RoleId::USER_ID,
            'uuid' => $uuid,
            'updated_at' => $updatedAt,
        ];

        // when
        $user = $repository->update($data);

        // then
        $this->assertNotNull($user);
        $this->seeInDatabase('users', ['email' => $email]);
        $this->seeInDatabase('users', ['uuid' => $uuid]);
        $this->seeInDatabase('users', ['updated_at' => $updatedAt]);
    }

    public function test_UpdatePassword_WhenUserExists_ThenUpdatePassword() : void
    {

        // given
        $data = [
            'id' => 1,
            'password' => 'C0c@C0l@P3psi',
            'login_attemps' => 0,
            'date' => Carbon::now(),
        ];
        $repository = new UserRepository();

        // when
        $user = $repository->updatePassword($data);

        // then
        $this->assertNotNull($user);
        $this->seeInDatabase('users', ['last_password_changed' => $data['date']]);
        $this->seeInDatabase('users', ['updated_at' => $data['date']]);
    }

    public function test_UpdateConfirmation_WhenUserExists_ThenUpdateAccountConfirmation() : void
    {

        // given
        $data = [
            'id' => 1,
            'date' => Carbon::now(),
        ];
        $repository = new UserRepository();

        // when
        $user = $repository->updateConfirmation($data);

        // then
        $this->assertNotNull($user);
        $this->seeInDatabase('users', ['updated_at' => $data['date']]);
    }

    public function test_UpdateLoginAttempts_WhenUserExists_ThenUpdateLoginAttempts() : void
    {

        // given
        $data = [
            'id' => 1,
            'login_attemps' => 2,
            'date' => Carbon::now(),
        ];
        $repository = new UserRepository();

        // when
        $user = $repository->updateLoginAttemps($data);

        // then
        $this->assertNotNull($user);
        $this->seeInDatabase('users', ['updated_at' => $data['date']]);
    }

    public function test_UpdateToken_WhenUserExists_ThenUpdateToken() : void
    {


        // given
        $date = Carbon::now();
        $data = [
            'id' => 1,
            'uuid' => Uuid::uuid4(),
            'expired_token' => $date->addMinutes(15),
            'updated_at' => $date,
        ];
        $repository = new UserRepository();

        // when
        $user = $repository->updateToken($data);

        // then
        $this->assertNotNull($user);
        $this->seeInDatabase('users', ['uuid' => $data['uuid'],]);
        $this->seeInDatabase('users', ['expired_token' => $data['expired_token'],]);
        $this->seeInDatabase('users', ['updated_at' => $data['updated_at'],]);
    }

    public function test_UpdateExpiredToken_WhenUserExists_ThenTokenUpdated() : void
    {

        // given
        $date = Carbon::now();
        $data = [
            'id' => 1,
            'date' => $date,
        ];
        $repository = new UserRepository();

        // when
        $user = $repository->updateExpiredToken($data);

        // then
        $this->assertEquals($data['date']->format('Y-m-d H:m:s'), $user->updated_at->format('Y-m-d H:m:s'));
        $this->assertEquals(
            $data['date']->addMinutes(env('TOKEN_EXPIRE'))->format('Y-m-d H:m:s'),
            $user->expired_token->format('Y-m-d H:m:s')
        );
    }

    public function test_ConfirmPassword_WhenDataIsOk_ThenReturnUser() : void
    {

        // given
        $id = 1;
        $user = User::find($id);
        $user->expired_token = Carbon::now()->addMinutes(env('TOKEN_EXPIRE'));
        $user->is_confirmed = false;
        $user->save();
        $repository = new UserRepository();

        // when
        $result = $repository->confirmAccount(['uuid' => $user->uuid]);

        // then
        $this->assertNotNull($result);
    }

    public function test_ConfirmPassword_WhenTokenExpired_ThenReturnNull() : void
    {

        // given
        $id = 1;
        $user = User::find($id);
        $user->expired_token = Carbon::now()->subMinutes(env('TOKEN_EXPIRE') + 1);
        $user->is_confirmed = false;
        $user->save();
        $repository = new UserRepository();

        // when
        $result = $repository->confirmAccount(['uuid' => $user->uuid]);

        // then
        $this->assertNull($result);
    }

    public function test_ConfirmPassword_WhenAccountIsConfirmed_ThenReturnNull() : void
    {

        // given
        $id = 1;
        $user = User::find($id);
        $user->expired_token = Carbon::now()->addMinutes(env('TOKEN_EXPIRE'));
        $user->is_confirmed = true;
        $user->save();
        $repository = new UserRepository();

        // when
        $result = $repository->confirmAccount(['uuid' => $user->uuid]);

        // then
        $this->assertNull($result);
    }

    public function test_ConfirmPassword_WhenUuidNotExists_ThenReturnNull() : void
    {

        // given
        $repository = new UserRepository();

        // when
        $result = $repository->confirmAccount(['uuid' => "uuid"]);

        // then
        $this->assertNull($result);
    }

    public function test_Destroy_WhenDestroyUser_ThenUserNotExists() : void
    {

        // given
        $id = 1;
        $repository = new UserRepository();

        // when
        $repository->destroy($id);
        $user = User::find($id);

        // then
        $this->assertNull($user);
    }
}
