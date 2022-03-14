<?php

use App\Enums\RoleId;
use App\Enums\StatusId;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\EventService;
use App\Services\ResponseService;
use App\Services\UserAdminService;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutEvents;
use Tests\TestCase;

class UserAdminServiceTest extends TestCase
{
    use WithoutEvents;

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
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_OK;

        // when
        $result = $service->findAll(base64_encode($user->uuid));

        // then
        $this->assertEquals($code, $result['code']);
    }

    public function test_FindAll_WhenUuidNotExists_ThenReturnUnathorizeCode() : void
    {

        // given
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $service->findAll(base64_encode("uuid"));

        // then
        $this->assertEquals($code, $result['code']);
    }

    public function test_FindById_WhenUserAdminExistsAndUserIdExists_ThenReturnUsersAndStatusOk() : void
    {

        // given
        $user = User::find(1);
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_OK;

        // when
        $result = $service->findById(base64_encode($user->uuid), $user->id);

        // then
        $this->assertEquals($code, $result['code']);
    }

    public function test_FindById_WhenUuidNotExistsAndUserIdExists_ThenReturnUnathorizeCode() : void
    {

        // given
        $user = User::find(1);
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $service->findById(base64_encode("uuid"), $user->id);

        // then
        $this->assertEquals($code, $result['code']);
    }

    public function test_FindById_WhenUuidExistsAndUserIdNotExists_ThenReturnNotFoundCode() : void
    {

        // given
        $user = User::find(1);
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_NOT_FOUND;

        // when
        $result = $service->findById(base64_encode($user->uuid), -1);

        // then
        $this->assertEquals($code, $result['code']);
    }

    public function test_Create_WhenNewUserDataIsValidate_ThenCreateNewUserAndReturnOkStatus() : void
    {
        // given
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
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
        $response = $service->create($data, $uuid);

        // then
        $this->assertEquals($errors, $response['errors']);
        $this->assertEquals($code, $response['code']);
    }

    public function test_Create_WhenUuidNotExists_ThenReturnUnathorized() : void
    {
        // given
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
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
        $response = $service->create($data, $uuid);

        // then
        $this->assertEquals($errors, $response['errors']);
        $this->assertEquals($code, $response['code']);
    }

    public function test_Create_WhenFiledsAreEmpty_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
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
        $response = $service->create($data, $uuid);

        // then
        $this->assertEquals($errors, $response['errors']);
        $this->assertEquals($code, $response['code']);
    }

    public function test_Create_WhenEmailIsBadFormat_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
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
        $response = $service->create($data, $uuid);

        // then
        $this->assertEquals($errors, $response['errors']);
        $this->assertEquals($code, $response['code']);
    }

    public function test_Create_WhenEmailExists_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
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
        $response = $service->create($data, $uuid);

        // then
        $this->assertEquals($errors, $response['errors']);
        $this->assertEquals($code, $response['code']);
    }

    public function test_Create_WhenPasswordToShort_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
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
        $response = $service->create($data, $uuid);

        // then
        $this->assertEquals($errors, $response['errors']);
        $this->assertEquals($code, $response['code']);
    }

    public function test_Create_WhenPasswordsMismatch_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
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
        $response = $service->create($data, $uuid);

        // then
        $this->assertEquals($errors, $response['errors']);
        $this->assertEquals($code, $response['code']);
    }

    public function test_Create_WhenRoleNotExists_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
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
        $response = $service->create($data, $uuid);

        // then
        $this->assertEquals($errors, $response['errors']);
        $this->assertEquals($code, $response['code']);
    }

    public function test_Create_WhenStatusNotExists_ThenReturnNotAcceptableStatus() : void
    {
        // given
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
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
        $response = $service->create($data, $uuid);

        // then
        $this->assertEquals($errors, $response['errors']);
        $this->assertEquals($code, $response['code']);
    }

    public function test_UpdateEmail_WhenAllDataIsOk_ThenUpdateEmailAndReturnOkStatus() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 2;
        $data = [
            'email' => 'email9999@example.com',
        ];
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_OK;

        // when
        $result = $service->updateEmail($data, base64_encode($user->uuid), $currentUserId);

        // then
        $this->assertEquals($result['code'], $code);
    }

    public function test_UpdateEmail_WhenUuidNotExists_ThenReturnUnathorizedStatus() : void
    {

        // given
        $currentUserId = 2;
        $data = [
            'email' => 'email9999@example.com',
        ];
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $service->updateEmail($data, base64_encode("uuid"), $currentUserId);

        // then
        $this->assertEquals($result['code'], $code);
    }

    public function test_UpdateEmail_WhenUserNotExists_ThenReturnNotFoundStatus() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 9999;
        $data = [
            'email' => 'email9999@example.com',
        ];
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_NOT_FOUND;

        // when
        $result = $service->updateEmail($data, base64_encode($user->uuid), $currentUserId);

        // then
        $this->assertEquals($result['code'], $code);
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
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $result = $service->updateEmail($data, base64_encode($user->uuid), $currentUserId);

        // then
        $this->assertEquals($result['code'], $code);
        $this->assertEquals($result['errors'], $errors);
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
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $result = $service->updateEmail($data, base64_encode($user->uuid), $currentUserId);

        // then
        $this->assertEquals($result['code'], $code);
        $this->assertEquals($result['errors'], $errors);
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
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_NOT_ACCEPTABLE;

        // when
        $result = $service->updateEmail($data, base64_encode($user->uuid), $currentUserId);

        // then
        $this->assertEquals($result['code'], $code);
        $this->assertEquals($result['errors'], $errors);
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
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_OK;

        // when
        $result = $service->updateRoleAndStatus($data, base64_encode($user->uuid), $currentUserId);

        // then
        $this->assertEquals($code, $result['code']);
    }

    public function test_UpdateRoleAndStatus_WhenUiidNotExists_ThenReturnUnathorized() : void
    {

        // given
        $currentUserId = 2;
        $data = [
            'role_id' => RoleId::ADMINISTRATOR_ID,
            'status_id' => StatusId::ACTIVE,
        ];
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $service->updateRoleAndStatus($data, base64_encode("uuid"), $currentUserId);

        // then
        $this->assertEquals($code, $result['code']);
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
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_NOT_FOUND;

        // when
        $result = $service->updateRoleAndStatus($data, base64_encode($user->uuid), $currentUserId);

        // then
        $this->assertEquals($code, $result['code']);
    }

    public function test_UpdateRoleAndStatus_WhenDataIsEmpty_ThenReturnNotAcceptable() : void
    {

        // given
        $user = User::find(1);
        $currentUserId = 2;
        $data = [];
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_NOT_ACCEPTABLE;
        $errors = [
            'role_id' => [0 => 'The role id field is required.'],
            'status_id' => [0 => 'The status id field is required.']
        ];

        // when
        $result = $service->updateRoleAndStatus($data, base64_encode($user->uuid), $currentUserId);

        // then
        $this->assertEquals($code, $result['code']);
        $this->assertEquals($errors, $result['errors']);
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
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_NOT_ACCEPTABLE;
        $errors = [
            'role_id' => [0 => 'The selected role id is invalid.'],
        ];

        // when
        $result = $service->updateRoleAndStatus($data, base64_encode($user->uuid), $currentUserId);

        // then
        $this->assertEquals($code, $result['code']);
        $this->assertEquals($errors, $result['errors']);
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
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_NOT_ACCEPTABLE;
        $errors = [
            'status_id' => [0 => 'The selected status id is invalid.'],
        ];

        // when
        $result = $service->updateRoleAndStatus($data, base64_encode($user->uuid), $currentUserId);

        // then
        $this->assertEquals($code, $result['code']);
        $this->assertEquals($errors, $result['errors']);
    }

    public function test_Destroy_WhenUserAdminExistsAndUserIdExists_ThenReturnUsersAndStatusOk() : void
    {

        // given
        $user = User::find(1);
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_OK;

        // when
        $result = $service->destroy(base64_encode($user->uuid), $user->id);

        // then
        $this->assertEquals($code, $result['code']);
    }

    public function test_Destroy_WhenUuidNotExistsAndUserIdExists_ThenReturnUnathorizeCode() : void
    {

        // given
        $user = User::find(1);
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_UNAUTHORIZED;

        // when
        $result = $service->destroy(base64_encode("uuid"), $user->id);

        // then
        $this->assertEquals($code, $result['code']);
    }

    public function test_Destroy_WhenUuidExistsAndUserIdNotExists_ThenReturnNotFoundCode() : void
    {

        // given
        $user = User::find(1);
        $service = new UserAdminService(new UserRepository(), new ResponseService(), new EventService());
        $code = Response::HTTP_NOT_FOUND;

        // when
        $result = $service->destroy(base64_encode($user->uuid), -1);

        // then
        $this->assertEquals($code, $result['code']);
    }
}
