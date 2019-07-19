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
use App\Services\Jira\ChartJsService;

class IssuesController extends Controller
{

    private $service;

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

        return view('cabinet.jira.issues.index', [
            'data' => $dataForChartJs
        ]);
    }
}
