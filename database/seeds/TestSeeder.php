<?php

use Illuminate\Database\Seeder;
use App\Entity\Jira\Issue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            try {
                DB::transaction(function () {
                    $issue = new Issue();
                    $issue->issue_id = 11149;
                    $issue->key = 'HELP-92';

                    $issue->save();
                    $issue->components()->sync([10300]);
                }, 5);
            } catch (Exception $e) {
                $errorMsg = sprintf(
                    'Error insert to database issues or users. %s.  Class - %s, line - %d',
                    $e->getMessage(),
                    __CLASS__,
                    __LINE__
                );
                dd($errorMsg);
            }


        }
    }
}
