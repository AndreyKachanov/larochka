<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

//Route::get('/test123', function () {
//    dump(\Illuminate\Support\Carbon::now()->format('d.m.Y H:i:s'));
//    $client = new GuzzleHttp\Client();
//    $test = $client->request('GET', 'https://api.vk.com/method/resolveScreenName', [
//        'delay' => '5000',
//        'query' => [
//            'screen_name' => 'obmenvalut_donetsk',
//            'access_token' => 'aa925c2e8a667e459f75892ba010bfab47c0cb2920e18cbc17491bce4e1b2829f135a86434e77c6f707c8',
//            'v' => '5.92'
//        ]
//    ]);
//    dump(json_decode($test->getBody()->getContents(), true));
//    dump(\Illuminate\Support\Carbon::now()->format('d.m.Y H:i:s'));
//})->name('test');
//
Route::get('/test234', function () {
    dump(\Illuminate\Support\Carbon::now()->subDays(10)->startOfDay());
})->name('test234');

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

//Route::get('/chat', function () {
//    return view('chat');
//})->name('chat');


Route::view('/chat', 'chat')->name('chat');
