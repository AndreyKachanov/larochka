<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 28.06.19
 * Time: 16:29
 */

namespace App\Http\Controllers\Cabinet\Jira;

use App\Entity\Jira\Line;
use App\Entity\Jira\Operator;
use App\Http\Controllers\Controller;

class OperatorsController extends Controller
{

    public function index()
    {
        $operators = Operator::with('rLine', 'rCreator')
            ->orderBy('line_id')
            ->orderBy('user_key')
            ->paginate(25);

        return view('cabinet.jira.operators.index', compact(
            'operators'
        ));
    }
}