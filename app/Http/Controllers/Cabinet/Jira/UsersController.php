<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 24.06.19
 * Time: 23:12
 */

namespace App\Http\Controllers\Cabinet\Jira;

use App\Entity\Jira\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        //$query = User::orderBy('user_key');
        //
        //if (!empty($value = $request->get('user_key'))) {
        //    $query->where('user_key', $value);
        //}
        //
        //if (!empty($value = $request->get('display_name'))) {
        //    $query->where('display_name', $value);
        //}
        //
        //if (!empty($value = $request->get('email'))) {
        //    $query->where('email', $value);
        //}

        $users =  User::orderBy('user_key')->paginate(25);

        return view('cabinet.jira.users.index', compact(
            'users'
        ));
    }
}
