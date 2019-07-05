<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;
use JiraRestApi\Issue\IssueService;


Route::get('/test123', function () {

    //$jql = 'created >= 2019-05-25 AND created <= 2019-06-25 AND assignee in (herasymchuk, chumak, sviridov, urvant, rezvanova, rizhuk, kostina, kondratska) AND creator not in (herasymchuk, chumak, sviridov, urvant, rezvanova, rizhuk, kostina, kondratska) ORDER BY created DESC';

    //$jql = 'key = HELP-11832';
    $jql = 'order by created asc';
    //$jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';

    try {
        $issueService = new IssueService();

        $pagination = -1;

        $startAt = 2;	//the index of the first issue to return (0-based)
        $maxResult = 2;	// the maximum number of jira to return (defaults to 50).
        $totalCount = -1;	// the number of jira to return
        // first fetch
        $ret = $issueService->search(
            $jql,
            $startAt,
            $maxResult,
            $fields = [
                '*all',
                //'summary',
                //'issuetype',
                //'creator',
                //'assignee',
                //'status',
                //'components',
                //'created'
            ],
            $expand = [
                'changelog',
                'renderedFields'
            ]
        );
        dd($ret);
        $totalCount = $ret->total;
    } catch (JiraException $e) {
        $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
    }

})->name('test');

Route::get('/test111', function () {
    $username = config('jira.user');
    $password = config('jira.password');

    //$url = 'https://xxx.atlassian.net/rest/api/2/Issue/Bug-5555';
    $url = 'https://sd.court.gov.ua/rest/api/2/issue/HELP-9903?expand=changelog';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

    $issue_list = (curl_exec($curl));
    dd(json_decode($issue_list));

})->name('test111');

Route::get('/test222', function () {
    try {
        $proj = new ProjectService();

        $p = $proj->get('HELP');

        dd($p);
    } catch (JiraException $e) {
        print("Error Occured! " . $e->getMessage());
    }

})->name('test111');
//
Route::get('/test234', function () {
    dump(\Illuminate\Support\Carbon::now()->subDays(10)->startOfDay());
})->name('test234');

Route::get('/test345', function () {
    try {
        $proj = new ProjectService();

        $prjs = $proj->getAllProjects();
        dd($prjs);

        foreach ($prjs as $p) {
            echo sprintf("Project Key:%s, Id:%s, Name:%s, projectCategory: %s\n",
                $p->key, $p->id, $p->name, $p->projectCategory['name']
            );
        }
    } catch (JiraException $e) {
        print("Error Occured! " . $e->getMessage());
    }
})->name('test345');

Route::get('/test678', function () {
    try {
        $us = new \JiraRestApi\User\UserService();

        $user = $us->get(['username' => 'a.kachanov']);

        dd($user);
    } catch (JiraException $e) {
        print("Error Occured! " . $e->getMessage());
    }
})->name('test678');


Route::get('/test', 'TestController@test')->name('test');
//Route::get('/test3', 'TestController@test3')->name('test3');

Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/login/phone', 'Auth\LoginController@phone')->name('login.phone');
Route::post('/login/phone', 'Auth\LoginController@verify');

Route::get('/verify/{token}', 'Auth\RegisterController@verify')->name('register.verify');

Route::group(
    [
        'prefix' => 'cabinet',
        'as' => 'cabinet.',
        'namespace' => 'Cabinet',
        'middleware' => ['auth'],
    ],
    function () {
        Route::get('/', 'HomeController@index')->name('home');

        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('/', 'ProfileController@index')->name('home');
            Route::get('/edit', 'ProfileController@edit')->name('edit');
            Route::put('/update', 'ProfileController@update')->name('update');
            Route::post('/phone', 'PhoneController@request');
            Route::get('/phone', 'PhoneController@form')->name('phone');
            Route::put('/phone', 'PhoneController@verify')->name('phone.verify');

            Route::post('/phone/auth', 'PhoneController@auth')->name('phone.auth');
        });

        Route::resource('messages', 'MessageController')->only(['index']);
        Route::resource('currencies', 'VkParsingPostsController')->only(['index']);

        Route::group(['prefix' => 'jira', 'as' => 'jira.'], function () {
            Route::get('/', 'Jira\IndexController@index')->name('home');

            Route::get('issues', 'Jira\IssuesController@index')->name('issues');
            Route::get('users', 'Jira\UsersController@index')->name('users');
            Route::get('operators', 'Jira\OperatorsController@index')->name('operators');
        });

        Route::post('parse', 'VkParsingPostsController@parse')->name('parse');

        Route::post('start_parse', 'VkParsingPostsController@startParse')->name('start_parse');
        Route::post('stop_parse', 'VkParsingPostsController@stopParse')->name('stop_parse');
    }
);


Route::group(
    [
        'prefix'     => 'admin',
        'as'         => 'admin.',
        'namespace'  => 'Admin',
        'middleware' => ['auth', 'can:admin-panel']
    ],
    function () {
        Route::get('/', 'HomeController@index')->name('home');
        Route::resource('users', 'UsersController');
        Route::post('/users/{user}/verify', 'UsersController@verify')->name('users.verify');
    }
);

Route::post('messages', function (Illuminate\Http\Request $request) {
    App\Events\PrivateChat::dispatch($request->all());
});

Route::get('/room/{room}', function (App\Entity\Chat\Room $room) {
    return view('room', ['room' => $room]);
})->middleware('auth')->name('room');

Route::view('/chat', 'chat')->name('chat');
