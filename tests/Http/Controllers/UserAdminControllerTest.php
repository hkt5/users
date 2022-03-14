<?php

namespace Tests\Http\Controllers;

use App\Enums\RoleId;
use App\Enums\StatusId;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutEvents;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserAdminControllerTest extends TestCase
{
    use WithoutEvents;
    use WithoutMiddleware;

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

    public function test_FindAll_WhenUserAdminExists_ThenReturnUsersAndStatusOk() : void
    {

        // given
        $user = User::find(1);
        $code = Response::HTTP_OK;

        // when
        $result = $this->get('/user-admin/all', ['Bareer' => base64_encode($user->uuid)]);

        // then
        $result->seeStatusCode($code);
    }

    public function test_FindAll_WhenUiidNotExists_ThenReturnUnathorizeCode() : void
    {

        // given
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $this->get('/user-admin/all', ['Bareer' => base64_encode("uuid")]);

        // then
        $result->seeStatusCode($code);
    }

    public function test_FindById_WhenUserAdminExistsAndUserIdExists_ThenReturnUsersAndStatusOk() : void
    {

        // given
        $user = User::find(1);
        $code = Response::HTTP_OK;

        // when
        $result = $this->get(
            '/user-admin/by-id/'.$user->id,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
    }

    public function test_FindById_WhenUuidNotExistsAndUserIdExists_ThenReturnUnathorizeCode() : void
    {

        // given
        $user = User::find(1);
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $this->get(
            '/user-admin/by-id/'.$user->id,
            ['Bareer' => base64_encode("uuid")]
        );

        // then
        $result->seeStatusCode($code);
    }

    public function test_FindById_WhenUuidExistsAndUserIdNotExists_ThenReturnNotFoundCode() : void
    {

        // given
        $user = User::find(1);
        $code = Response::HTTP_NOT_FOUND;

        // when
        $result = $this->get(
            '/user-admin/by-id/'.-1,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
    }

    public function test_Create_WhenNewUserDataIsValidate_ThenCreateNewUserAndReturnOkStatus() : void
    {
        // given
        $data = [
            'email' => 'email9999@example.com',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssw0rd',
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE,
        ];
        $uuid = base64_encode(User::find(1)->uuid);
        $errors = null;
        $code = Response::HTTP_OK;

        // when
        $response = $this->post('/user-admin/create', $data, ['Bareer' => $uuid]);

        // then
        $response->seeStatusCode($code);
        $response->seeJsonContains(['errors' => $errors]);
    }

    public function test_Create_WhenUuidNotExists_ThenReturnUnathorized() : void
    {
        // given
        $data = [
            'email' => 'email9999@example.com',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssw0rd',
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE,
        ];
        $uuid = "uuid";
        $errors = null;
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $response = $this->post('/user-admin/create', $data, ['Bareer' => base64_encode($uuid)]);

        // then
        $response->seeStatusCode($code);
        $response->seeJsonContains(['errors' => $errors]);
    }

    public function test_Create_WhenFiledsAreEmpty_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $data = [];
        $uuid = base64_encode(User::find(1)->uuid);
        $errors = [
            'email' => [0 => 'The email field is required.'],
            'password' => [0 => 'The password field is required.'],
            'password_confirmation' => [0 => 'The password confirmation field is required.'],
            'role_id' => [0 => 'The role id field is required.'],
            'status_id' => [0 => 'The status id field is required.'],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $response = $this->post('/user-admin/create', $data, ['Bareer' => $uuid]);

        // then
        $response->seeStatusCode($code);
        $response->seeJsonContains(['errors' => $errors]);
    }

    public function test_Create_WhenEmailIsBadFormat_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $data = [
            'email' => 'email9999',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssw0rd',
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE,
        ];
        $uuid = base64_encode(User::find(1)->uuid);
        $errors = [
            'email' => [0 => 'The email must be a valid email address.'],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $response = $this->post('/user-admin/create', $data, ['Bareer' => $uuid]);

        // then
        $response->seeStatusCode($code);
        $response->seeJsonContains(['errors' => $errors]);
    }

    public function test_Create_WhenEmailExists_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $data = [
            'email' => 'email@example.com',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssw0rd',
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE,
        ];
        $uuid = base64_encode(User::find(1)->uuid);
        $errors = [
            'email' => [0 => 'The email has already been taken.'],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $response = $this->post('/user-admin/create', $data, ['Bareer' => $uuid]);

        // then
        $response->seeStatusCode($code);
        $response->seeJsonContains(['errors' => $errors]);
    }

    public function test_Create_WhenPasswordToShort_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $data = [
            'email' => 'email999@example.com',
            'password' => 'P@ssw0rdP@s',
            'password_confirmation' => 'P@ssw0rdP@s',
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE,
        ];
        $uuid = base64_encode(User::find(1)->uuid);
        $errors = [
            'password' => [0 => 'The password must be at least 12 characters.'],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $response = $this->post('/user-admin/create', $data, ['Bareer' => $uuid]);

        // then
        $response->seeStatusCode($code);
        $response->seeJsonContains(['errors' => $errors]);
    }

    public function test_Create_WhenPasswordsMismatch_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $data = [
            'email' => 'email999@example.com',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssword',
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE,
        ];
        $uuid = base64_encode(User::find(1)->uuid);
        $errors = [
            'password' => [0 => 'The password confirmation does not match.'],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $response = $this->post('/user-admin/create', $data, ['Bareer' => $uuid]);

        // then
        $response->seeStatusCode($code);
        $response->seeJsonContains(['errors' => $errors]);
    }

    public function test_Create_WhenRoleNotExists_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $data = [
            'email' => 'email9999@example.com',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssw0rd',
            'role_id' => 15,
            'status_id' => StatusId::ACTIVE,
        ];
        $uuid = base64_encode(User::find(1)->uuid);
        $errors = [
            'role_id' => [0 => 'The selected role id is invalid.'],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $response = $this->post('/user-admin/create', $data, ['Bareer' => $uuid]);

        // then
        $response->seeStatusCode($code);
        $response->seeJsonContains(['errors' => $errors]);
    }

    public function test_Create_WhenStatusNotExists_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $data = [
            'email' => 'email999@example.com',
            'password' => 'P@ssw0rdP@ssw0rd',
            'password_confirmation' => 'P@ssw0rdP@ssw0rd',
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => 11,
        ];
        $uuid = base64_encode(User::find(1)->uuid);
        $errors = [
            'status_id' => [0 => 'The selected status id is invalid.'],
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $response = $this->post('/user-admin/create', $data, ['Bareer' => $uuid]);

        // then
        $response->seeStatusCode($code);
        $response->seeJsonContains(['errors' => $errors]);
    }

    public function test_UpdateEmail_WhenAllDataIsOk_ThenUpdateEmailAndReturnOkStatus() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 2;
        $data = [
            'email' => 'email9999@example.com',
        ];
        $code = Response::HTTP_OK;

        // when
        $result = $this->put('/user-admin/update-email/'.$currentUserId, $data, ['Bareer' => base64_encode($user->uuid)]);

        // then
        $result->seeStatusCode($code);
    }

    public function test_UpdateEmail_WhenUuidNotExists_ThenReturnUnathorizedStatus() : void
    {

        // given
        $currentUserId = 2;
        $data = [
            'email' => 'email9999@example.com',
        ];
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $this->put('/user-admin/update-email/'.$currentUserId, $data, ['Bareer' => base64_encode("uuid")]);

        // then
        $result->seeStatusCode($code);
    }

    public function test_UpdateEmail_WhenUserNotExists_ThenReturnNotFoundStatus() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 9999;
        $data = [
            'email' => 'email9999@example.com',
        ];
        $code = Response::HTTP_NOT_FOUND;

        // when
        $result = $this->put('/user-admin/update-email/'.$currentUserId, $data, ['Bareer' => base64_encode($user->uuid)]);

        // then
        $result->seeStatusCode($code);
    }

    public function test_UpdateEmail_WhenDataIsEmpty_ThenReturnNotAcceptableStatus() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 2;
        $data = [];
        $errors = [
            'email' => [0 => 'The email field is required.']
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $result = $this->put('/user-admin/update-email/'.$currentUserId, $data, ['Bareer' => base64_encode($user->uuid)]);

        // then
        $result->seeStatusCode($code);
        $result->seeJsonContains(['errors' => $errors]);
    }

    public function test_UpdateEmail_WhenEmailIsBadFormat_ThenReturnNotAcceptableStatus() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 2;
        $data = [
            'email' => 'email999'
        ];
        $errors = [
            'email' => [0 => 'The email must be a valid email address.']
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $result = $this->put('/user-admin/update-email/'.$currentUserId, $data, ['Bareer' => base64_encode($user->uuid)]);

        // then
        $result->seeStatusCode($code);
        $result->seeJsonContains(['errors' => $errors]);
    }

    public function test_UpdateEmail_WhenEmailExists_ThenReturnNotAcceptableStatus() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 2;
        $data = [
            'email' => 'email@example.com'
        ];
        $errors = [
            'email' => [0 => 'The email has already been taken.']
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $result = $this->put('/user-admin/update-email/'.$currentUserId, $data, ['Bareer' => base64_encode($user->uuid)]);

        // then
        $result->seeStatusCode($code);
        $result->seeJsonContains(['errors' => $errors]);
    }

    public function test_UpdateRoleAndStatus_WhenDataIsOk_ThenUpdateRoleAndStatusAndReturnOk() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 2;
        $data = [
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE,
        ];
        $code = Response::HTTP_OK;

        // when
        $result = $this->put(
            '/user-admin/update-role-and-status/'.$currentUserId,
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
    }

    public function test_UpdateRoleAndStatus_WhenUiidNotExists_ThenReturnUnathorized() : void
    {

        // given
        $currentUserId = 2;
        $data = [
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE,
        ];
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $this->put(
            '/user-admin/update-role-and-status/'.$currentUserId,
            $data,
            ['Bareer' => base64_encode("uuid")]
        );

        // then
        $result->seeStatusCode($code);
    }

    public function test_UpdateRoleAndStatus_WhenUserNotExists_ThenReturnNotFound() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 9999;
        $data = [
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE,
        ];
        $code = Response::HTTP_NOT_FOUND;

        // when
        $result = $this->put(
            '/user-admin/update-role-and-status/'.$currentUserId,
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
    }

    public function test_UpdateRoleAndStatus_WhenDataIsEmpty_ThenReturnNotAcceptable() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 2;
        $data = [];
        $code = Response::HTTP_NOT_ACCEPTABLE;
        $errors = [
            'role_id' => [0 => 'The role id field is required.'],
            'status_id' => [0 => 'The status id field is required.']
        ];

        // when
        $result = $this->put(
            '/user-admin/update-role-and-status/'.$currentUserId,
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
        $result->seeJsonContains($errors);
    }

    public function test_UpdateRoleAndStatus_WhenRoleNotExists_ThenReturnNotAcceptable() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 2;
        $data = [
            'role_id' => 9999,
            'status_id' => StatusId::ACTIVE,
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;
        $errors = [
            'role_id' => [0 => 'The selected role id is invalid.'],
        ];

        // when
        $result = $this->put(
            '/user-admin/update-role-and-status/'.$currentUserId,
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
        $result->seeJsonContains($errors);
    }

    public function test_UpdateRoleAndStatus_WhenStatusNotExists_ThenReturnNotAcceptable() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 2;
        $data = [
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => 99999,
        ];
        $code = Response::HTTP_NOT_ACCEPTABLE;
        $errors = [
            'status_id' => [0 => 'The selected status id is invalid.'],
        ];

        // when
        $result = $this->put(
            '/user-admin/update-role-and-status/'.$currentUserId,
            $data,
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
        $result->seeJsonContains($errors);
    }

    public function test_Destroy_WhenUserAdminExistsAndUserIdExists_ThenReturnUsersAndStatusOk() : void
    {

        // given
        $user = User::find(1);
        $code = Response::HTTP_OK;

        // when
        $result = $this->delete(
            '/user-admin/delete/'.$user->id,
            [],
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
    }

    public function test_Destroy_WhenUuidNotExistsAndUserIdExists_ThenReturnUnathorizeCode() : void
    {

        // given
        $user = User::find(1);
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $this->delete(
            '/user-admin/delete/'.$user->id,
            [],
            ['Bareer' => base64_encode("uuid")]
        );

        // then
        $result->seeStatusCode($code);
    }

    public function test_Destroy_WhenUuidExistsAndUserIdNotExists_ThenReturnNotFoundCode() : void
    {

        // given
        $user = User::find(1);
        $code = Response::HTTP_NOT_FOUND;

        // when
        $result = $this->delete(
            '/user-admin/delete/'.-1,
            [],
            ['Bareer' => base64_encode($user->uuid)]
        );

        // then
        $result->seeStatusCode($code);
    }
}
