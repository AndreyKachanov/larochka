<?php

namespace App\Http\Controllers;

use App\Entity\Jira\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {

        $query = $request->get('query');
        $users = User::where('user_key','like','%'.$query.'%')->get();
        //dd($users);
        return response()->json($users);
    }
}
