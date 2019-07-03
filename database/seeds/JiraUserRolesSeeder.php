<?php

use Illuminate\Database\Seeder;
use App\Entity\Jira\User\Role;

//@codingStandardsIgnoreLine
class JiraUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Role::count() === 0) {
            Role::create(['name' => 'user']);
            Role::create(['name' => 'l1']);
            Role::create(['name' => 'l2']);
            Role::create(['name' => 'development']);
        }
    }
}
