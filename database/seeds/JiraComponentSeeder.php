<?php

use Illuminate\Database\Seeder;
use App\Entity\Jira\Component;

class JiraComponentSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            if (Component::count() == 0) {
                Component::create([
                    'name'    => 'феміда',
                    'jira_id' => 10536,
                ]);

                Component::create([
                    'name'    => 'СПД',
                    'jira_id' => 10505,
                ]);
                Component::create([
                    'name'    => 'Квитанции',
                    'jira_id' => 10534,
                ]);
            }
        }
    }
}
