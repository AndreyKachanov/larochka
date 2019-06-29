<?php

namespace App\Http\Controllers\Cabinet\Jira;

use App\Entity\Jira\Issue;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {


        return view('cabinet.jira.index');
    }
}
