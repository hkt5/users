<?php

namespace Database\Seeders;

use App\Enums\RoleId;
use App\Enums\StatusId;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
            [
                [
                    'email' => 'email@example.com',
                    'password' => Hash::make('P@ssw0rd'),
                    'uuid' => Uuid::uuid4(),
                    'status_id' => StatusId::ACTIVE,
                    'role_id' => RoleId::ADMINISTRATOR_ID,
                    'login_attemps' => 0,
                    'is_confirmed' => true,
                    'last_password_changed' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'email' => 'email1@example.com',
                    'password' => Hash::make('P@ssw0rd'),
                    'uuid' => Uuid::uuid4(),
                    'status_id' => StatusId::ACTIVE,
                    'role_id' => RoleId::BUSINESS_OWNER_ID,
                    'login_attempts' => 0,
                    'is_confirmed' => true,
                    'last_password_changed' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'email' => 'email3@example.com',
                    'password' => Hash::make('P@ssw0rd'),
                    'uuid' => Uuid::uuid4(),
                    'status_id' => StatusId::INACTIVE,
                    'role_id' => RoleId::EMPLOYEE_ID,
                    'login_attempts' => 0,
                    'is_confirmed' => true,
                    'last_password_changed' => Carbon::now()->subDays(31),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]
        );
    }
}
