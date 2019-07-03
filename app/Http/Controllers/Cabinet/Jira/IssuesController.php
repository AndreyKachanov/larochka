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
        //dd(Issue::find('11149')->rCreator->rOperator->rLine->name);
        //$issues = Issue::with('rAssignee')->get();
        //$issues = Issue::with('rAssignee.rOperator.rLine')->get();

        Issue::whereHas('rAssignee', function ($creator) {
            /** @var User $creator */
            $creator->whereUserKey('a.kachanov');
            $creator->whereHas('rOperator', function ($operator) {
                /** @var Operator $operator */
                $operator->whereHas('rLine', function ($line) {
                    /** @var Line $line */
                    //$line->whereName('L1');
                });
            });
        })->whereHas('rComponents', function ($component) {
            /** @var Component $component */
            $component->whereName('Квитанции');
        })->get()->dump();


        return view('cabinet.jira.issues.index');
    }
}
