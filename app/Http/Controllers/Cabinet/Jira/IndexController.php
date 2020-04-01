<?php

namespace App\Http\Controllers\Cabinet\Jira;

use App\Entity\Jira\Issue;
use App\Http\Controllers\Controller;
use App\Services\Jira\ChartJsService;

class IndexController extends Controller
{
    private $service;

    /**
     * IndexController constructor.
     * @param ChartJsService $service
     */
    public function __construct(ChartJsService $service)
    {
        $this->middleware('can:jira');
        $this->service = $service;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $dataForChartJs = $this->service->getDataToChartJs();
        //dd($dataForChartJs);

        return view('cabinet.jira.index', [
            'data' => $dataForChartJs
        ]);
    }
}