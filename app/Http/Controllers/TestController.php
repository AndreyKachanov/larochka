<?php

namespace App\Http\Controllers;

use App\Events\PublicChat;

class TestController extends Controller
{
    public function test()
    {
        //PublicChat::dispatch("get my message");
        return view('test');
    }
}
