<?php

namespace App\Http\Controllers\Cabinet;

use App\Entity\Vkontakte\Post;
use App\Events\PrivateTest;
use App\Events\PublicChat;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class VkontakteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$accessToken = config('vk.access_token');
        //$client = new Client();
        //$request = "https://api.vk.com/method/wall.get?owner_id=-87785879&access_token=$accessToken&v=5.92&count=100";
        //
        //try {
        //    $response = $client->get($request)->getBody()->getContents();
        //    $json = json_decode($response, true);
        //    if (isset($json['error'])) {
        //        $error = $json['error'];
        //        dd('Response vk. error_code -' . $error['error_code'] . ', error_msg - ' . $error['error_msg']);
        //    }
        //} catch (Exception $e) {
        //    dd("Error - " . $e->getMessage() . ', line - ' . $e->getLine() . ' File - ' . $e->getFile());
        //}
        //
        //$items = $json['response']['items'];
        //
        //foreach ($items as $item) {
        //    broadcast(new PrivateTest($item['text']));
        //}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entity\Vkontakte\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Entity\Vkontakte\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entity\Vkontakte\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entity\Vkontakte\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
