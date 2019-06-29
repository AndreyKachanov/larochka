<?php

use Illuminate\Database\Seeder;
use App\Entity\Jira\Operator;

class JiraOperatorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $l1 = [
            'fomina',
            'karnash',
            'kondratska',
            'kostina',
            'maksiuta.y',
            'maksyuta',
            'rizhuk',
            'stepina',
        ];

        $l2 = [
            'a.kachanov',
            'chumak',
            'herasymchuk',
            'rezvanova',
            'sviridov',
            'urvant',
        ];

        if (Operator::count() == 0) {
            foreach ($l1 as $operator) {
                Operator::create([
                    'user_key'     => $operator,
                    'line_id' => 1
                ]);
            }
            foreach ($l2 as $operator) {
                Operator::create([
                    'user_key'     => $operator,
                    'line_id' => 2
                ]);
            }
        }
    }
}
