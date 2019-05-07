<?php

use Illuminate\Database\Seeder;
use App\Entity\User\User;
use Illuminate\Support\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            if (User::count() == 0) {
                User::create([
                    'email'             => 'test@test.loc',
                    'name'              => 'Andreii',
                    'last_name'         => 'Testov',
                    'phone'             => '380997111111',
                    'phone_auth'        => false,
                    'phone_verified'    => false,
                    'email_verified_at' => Carbon::now(),
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                    'status'            => User::STATUS_ACTIVE,
                    'role'              => User::ROLE_ADMIN,
                    'password'          => Hash::make('qwerty')
                ]);
            }
        }
    }
}
