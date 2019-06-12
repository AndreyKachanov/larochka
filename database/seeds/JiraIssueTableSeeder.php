<?php

use Illuminate\Database\Seeder;
use App\Entity\Jira\Issue;
use Illuminate\Support\Carbon;

class JiraIssueTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            if (Issue::count() == 0) {
                $issue = Issue::create([
                    'jira_id'         => 11111,
                    'key'             => 'HELP-9999',
                    'summary'         => 'Налаштування параметров авторозподилу',
                    'issue_type'      => 'Сервисный запрос',
                    'creator'         => 'a.kachanov',
                    'assignee'        => 'a.kachanov',
                    'status'          => 'Виконано',
                    'created_in_jira' => Carbon::now()->subDays(5)
                ]);
            }
        }
    }
}
