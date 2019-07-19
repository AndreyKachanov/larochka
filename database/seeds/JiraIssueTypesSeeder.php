<?php

use Illuminate\Database\Seeder;
use App\Entity\Jira\Issue\Type;

//@codingStandardsIgnoreLine
class JiraIssueTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Type::count() === 0) {
            $issueTypes = config('jira.issue_types');

            if ($issueTypes === null) {
                dd("config jira.issue_types === null");
            }

            if (count($issueTypes) === 0) {
                dd("config jira.issue_types. count array = 0");
            }

            foreach ($issueTypes as $type) {
                Type::create(['name' => $type['name']]);
            }
        }
    }
}
