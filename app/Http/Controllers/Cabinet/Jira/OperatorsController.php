<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 28.06.19
 * Time: 16:29
 */

namespace App\Http\Controllers\Cabinet\Jira;

use App\Entity\Jira\User;
use App\Http\Controllers\Controller;

class OperatorsController extends Controller
{

    public function index()
    {
        $line1 = User::whereRoleId(2)->get();
        $line2 = User::whereRoleId(3)->get();

        return view('cabinet.jira.operators.index', compact(
            'line1',
            'line2'
        ));
    }
}