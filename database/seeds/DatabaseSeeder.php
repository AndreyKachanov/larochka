<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        //$this->call(JiraUserTableSeeder::class);
        //$this->call(JiraComponentSeeder::class);
        //$this->call(JiraIssueTableSeeder::class);
    }
}
