<?php

namespace App\Http\Controllers\Cabinet;

use App\Entity\Email\Message;
use App\Events\PrivateTest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use DateTime;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:show-parser');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Message::orderByDesc('date_received');

        if (!empty($value = $request->get('mobile'))) {
            $query->where('mobile', $value);
        }

        if (!empty($value = $request->get('date_received'))) {
            $query->where('date_received', new DateTime($value));
        }

        if (!empty($value = $request->get('email'))) {
            $query->where('email', $value);
        }

        if (!empty($value = $request->get('sender'))) {
            $query->where('sender', $value);
        }

        if (!empty($value = $request->get('ip_address'))) {
            $query->where('ip_address', $value);
        }

        if (!empty($value = $request->get('isp'))) {
            $query->where('isp', $value);
        }

        if (!empty($value = $request->get('city'))) {
            $query->where('city', $value);
        }

        if (!empty($value = $request->get('region_name'))) {
            $query->where('region_name', $value);
        }

        if (!empty($value = $request->get('country'))) {
            $query->where('country', $value);
        }

        $messages = $query->paginate(20);

        $emails = Cache::tags('emails')->rememberForever('emails', function () {
            return Message::whereNotNull('email')->distinct()->orderBy('email')->get('email');
        });
        $senders = Cache::tags('senders')->rememberForever('senders', function () {
            return Message::whereNotNull('sender')->distinct()->orderBy('sender')->get('sender');
        });
        $ipAddress = Cache::tags('ipAddress')->rememberForever('ipAddress', function () {
            return Message::whereNotNull('ip_address')->distinct()->orderBy('ip_address')->get('ip_address');
        });
        $regions = Cache::tags('regions')->rememberForever('regions', function () {
            return Message::whereNotNull('region_name')->distinct()->orderBy('region_name')->get('region_name');
        });
        $cities = Cache::tags('cities')->rememberForever('cities', function () {
            return Message::whereNotNull('city')->distinct()->orderBy('city')->get('city');
        });
        $providers = Cache::tags('providers')->rememberForever('providers', function () {
            return Message::whereNotNull('isp')->distinct()->orderBy('isp')->get('isp');
        });
        $countries = Cache::tags('countries')->rememberForever('countries', function () {
            return Message::whereNotNull('country')->distinct()->orderBy('country')->get('country');
        });

        $isMobile = Message::distinct()->orderBy('mobile')->get('mobile');

        return view('cabinet.messages.index', compact(
            'messages',
            'emails',
            'senders',
            'ipAddress',
            'regions',
            'cities',
            'providers',
            'countries',
            'isMobile'
        ));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
