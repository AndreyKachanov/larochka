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
        //$cache =  Cache::get('parsing_vk_groups')[Auth::user()->id] ?? [];
        //if (!empty($cache)) {
        //    foreach ($cache['groups'] as $group) {
        //        $arr[]['name'] = $group['name'];
        //    }
        //
        //    $cache['groups'] = $arr;
        //}

        //return view('cabinet.currencies.index', compact('cache'));
        return view('cabinet.currencies.parser');
    }

    /**
     * @param ParsingVkPostsRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function parse(ParsingVkPostsRequest $request)
    {
        $userId = Auth::id();
        //$groupsFromVk = $this->service->getGroups($request->get('groups'));
        $groupsFromVk = Cache::get('parsing_vk_groups_live')[$userId];
        //dd($groupsFromVk);

        $this->service->sendDataToPusher(
            $userId,
            $groupsFromVk,
            $request->get('keywords'),
            $request->get('days')
        );

        return response()->json('data sent');
    }

    public function startParse(ParsingVkPostsRequest $request)
    {
        $groupsFromUser = $request->get('groups');
        $keywords = $request->get('keywords');
        $days = $request->get('days');

        $groupsFromVk = $this->service->getGroups($groupsFromUser);
        $userId = Auth::user()->id;
        $this->service->setParsingDataFromUser($userId, $groupsFromVk, $keywords, $days);

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
