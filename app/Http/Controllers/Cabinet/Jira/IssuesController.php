<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 24.06.19
 * Time: 23:12
 */

namespace App\Http\Controllers\Cabinet\Jira;

use App\Entity\Jira\Component;
use App\Entity\Jira\User;
use App\Entity\Jira\Issue;
use App\Entity\Jira\Line;
use App\Entity\Jira\Operator;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;

class IssuesController extends Controller
{

    public function index()
    {
        return view('cabinet.jira.issues.index');
    }
}
