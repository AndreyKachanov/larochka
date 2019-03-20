<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 13.12.18
 * Time: 10:28
 */

namespace App\Http\Controllers\Cabinet;


use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        return view('cabinet.home');
    }

}