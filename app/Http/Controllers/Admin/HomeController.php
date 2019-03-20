<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 25.12.18
 * Time: 14:43
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('admin.home');
    }
}