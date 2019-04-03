<?php

namespace App\Http\Controllers\Cabinet;

use App\Entity\Vkontakte\Post;
use App\Http\Requests\Cabinet\ParsingVkPostsRequest;
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

    public function startParse(ParsingVkPostsRequest $request)
    {
        $groupsFromUser = $request->get('groups');
        $keywords = $request->get('keywords');
        $groupsFromVk = $this->service->getGroups($groupsFromUser);

        $userId = Auth::user()->id;
        $this->service->setParsingDataFromUser($userId, $groupsFromVk, $keywords);

        dd(Cache::get('parsing_vk_groups'));

        return response()->json(Cache::get('parsing_vk_groups'));
    }

    public function stopParse()
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
