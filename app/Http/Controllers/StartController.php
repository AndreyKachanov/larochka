<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 10.07.19
 * Time: 22:52
 */

namespace App\Http\Controllers;

use App\Entity\Jira\User;

class StartController
{

    public function chartData()
    {
        $operators = User::whereRoleId(2)->orWhere('role_id', 3)->orderBy('role_id')->pluck('user_key')->toArray();
        $issues = User::whereRoleId(2)->orWhere('role_id', 3)->orderBy('role_id')->with('rIssues')->get();
        $issuesArray = [];
        foreach ($issues as $item) {
            $issuesArray[] = $item->rIssues->count();
        }
        return [
            'labels'   => $operators,
            'datasets' => [
                [
                    'label'           => 'Задачи',
                    'backgroundColor' => '#F26202',
                    'data'            => $issuesArray,
                ]
            ]
        ];
    }
}
