<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 17.07.19
 * Time: 21:11
 */

namespace App\Services\Jira;

use App\Entity\Jira\Issue;
use App\Entity\Jira\Issue\Type;
use App\Entity\Jira\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ChartJsService
{
    /**
     * @return array
     */
    public function getDataToChartJs(): array
    {
        if (User::whereRoleId(2)->orWhere('role_id', 3)->count() == 0) {
            dd('no users have roles L1 and L2');
        }

        $issueTypes = config('jira.issue_types');

        if ($issueTypes === null) {
            dd("config jira.issue_types === null");
        }

        if (count($issueTypes) === 0) {
            dd("config jira.issue_types. count array = 0");
        }

        $operators = User::whereRoleId(2)
            ->orWhere('role_id', 3)
            ->orderBy('display_name')->get();

        $labels = array_map(function (string $item) {
            return trim(explode(' ', $item)[0]);
        }, $operators->pluck('display_name')->toArray());

        $arrayToChartJs = [];

        $startData = Carbon::now()->subMonth()->startOfMonth()->addDays(24);
        $endDate = Carbon::now()->startOfMonth()->addDays(24);

        dump($startData);
        dd($endDate);

        foreach ($issueTypes as $type) {
            if ($type['name'] != 'Інші проекти') {
                $typeId = Type::where('name', $type['name'])->pluck('id')->toArray()[0];

                $arrayByType = Issue::select(DB::raw('assignee, count(*) as cnt'))
                    ->where('created_at_jira', '>=', $startData)
                    ->where('created_at_jira', '<=', $endDate)
                    ->where('issue_type_id', $typeId)
                    ->with('rAssignee:user_key,display_name')
                    ->whereHas('rAssignee', function (Builder $query) use ($type) {
                        $query->where('role_id', 2)->orWhere('role_id', 3);
                    })->groupBy('assignee')
                    ->orderByDesc('cnt')
                    ->get()
                    ->toArray();

                //dd($arrayByType);
                $newArr = [];
                foreach ($arrayByType as $item) {
                    $key = $item['assignee'];
                    $newArr[$key]['cnt'] = $item['cnt'];
                    $newArr[$key]['display_name'] = $item['r_assignee']['display_name'];
                }

                $arrayToChartJs[] = [
                    'label' => $type['name'],
                    'backgroundColor' => $type['backgroundColor'],
                    'borderColor' => $type['borderColor'],
                    'borderWidth' => 1,
                    'data' => $this->transformDataToChartJs($newArr, $operators)
                ];
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $arrayToChartJs
        ];
    }

    /**
     * @param array $newArr
     * @param Collection $operators
     * @return array
     */
    private function transformDataToChartJs(array $newArr, Collection $operators): array
    {

        $data = [];
        /** @var User $user */
        foreach ($operators as $user) {
            /** @var User $user */
            if (!isset($newArr[$user->user_key])) {
                $data[] = 0;
            } else {
                $data[] = $newArr[$user->user_key]['cnt'];
            }
        }

        //dd($data);
        return $data;
    }
}
