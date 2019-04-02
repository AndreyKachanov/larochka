<?php

namespace App\Http\Controllers\Cabinet;

use App\Entity\Vkontakte\Post;
use App\Services\Vk\ParsingPostsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Cache;

class VkParsingPostsController extends Controller
{

    private $service;

    public function __construct(ParsingPostsService $service)
    {
        $this->middleware('can:vk-parser');
        $this->service = $service;
    }

    public function index()
    {
        return view('cabinet.currencies.index');
    }

    public function run(Request $request)
    {

        $groupsFromUser = $request->get('groups');
        $keywords = $request->get('keywords');

        //проверка, передали ли с фронта какие-то группы
        if ($groupsFromUser === null || count($groupsFromUser) === 0) {
            return response()->json('not found vk group || vkgroups = []');
        }

        //проверка, передали ли с фронта ключевые слова
        if ($keywords === null || $keywords === '') {
            return response()->json('not found keywords');
        }

        $groupsFromVk = $this->service->getGroups($groupsFromUser);
        //dd($groupsFromVk);

        if (empty($groupsFromVk)) {
            return response()->json('из фронта передали не существующие группы');
        }

        $userId = Auth::user()->id;
        $this->service->setParsingDataFromUser($userId, $groupsFromVk, $keywords);

        dd(Cache::get('parsing_vk_groups'));

        return response()->json(Cache::get('parsing_vk_groups'));
    }

    public function stop()
    {
        $userId = Auth::user()->id;
        $this->service->stopParsingFromUser($userId);
        dd(Cache::get('parsing_vk_groups'));
        return response()->json(Cache::get('parsing_vk_groups'));
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
