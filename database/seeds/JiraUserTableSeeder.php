<?php

use Illuminate\Database\Seeder;
use App\Entity\Jira\Creator;

class JiraUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            if (Creator::count() == 0) {
                Creator::create([
                    'user_key'     => 'a.kachanov',
                    'display_name' => 'Андрей Качанов',
                    'email'        => 'test@test.loc',
                    'avatar'       => 'https://sd.court.gov.ua/secure/useravatar?ownerId=a.kachanov&avatarId=11910',
                ]);
                Creator::create([
                    'user_key'     => 'chumak',
                    'display_name' => 'Фудор Чумак',
                    'email'        => 'test@test1.loc',
                    'avatar'       => 'https://sd.court.gov.ua/secure/useravatar?ownerId=a.kachanov&avatarId=11910',
                ]);
            }
        }
    }
}
