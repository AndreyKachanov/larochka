<?php

namespace App\Console\Commands\Jira;

use App\Entity\Jira\Issue;
use App\Services\Jira\ParsingIssuesService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use JiraRestApi\Issue\Issue as JiraIssue;

class ParseIssuesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:issues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parsing jira issues';
    /**
     * @var ParsingIssuesService
     */
    private $service;

    /**
     * ParseIssuesCommand constructor.
     * @param ParsingIssuesService $service
     */
    public function __construct(ParsingIssuesService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    public function handle(): bool
    {
        $fetchedCount = (int)config('jira.fetched_count');
        $projectName = config('jira.project_name');

        if ($fetchedCount <= 0 || $fetchedCount > 10000) {
            $this->error("Value fetchedCount not must be <= 0 || >= 10000");
            return false;
        }

        if ($projectName == "") {
            $this->error("Value in .env JIRA_PROJECT_NAME must be contain characters");
            return false;
        }

        //если бд пуcтая
        if (Issue::count() == 0) {
            $totalCountIssues = $this->service->getTotalCountIssues($projectName);
            if ($totalCountIssues > 0) {
                //извлекаем первые задачи
                $jql = sprintf(
                    'project = %s ORDER BY key ASC',
                    $projectName
                );
                return $this->handleIssues($jql, $fetchedCount);
            }
        //если в бд есть записи - дополняем новыми задачами из джиры
        } else {
            $lastIssueKey = Issue::orderByDesc('key')->first()->key;
            $jql = sprintf(
                'project = %s AND key > HELP-%s ORDER BY key ASC',
                $projectName,
                $lastIssueKey
            );

            return $this->handleIssues($jql, $fetchedCount);
        }
        return false;
    }

    /**
     * @param string $jql
     * @param int $fetchedCount
     * @return bool
     * @throws \Throwable
     */
    private function handleIssues(string $jql, int $fetchedCount): bool
    {
        $resultMsg = '[' . Carbon::now()->toDateTimeString() . ']. ' . 'No new issues.';
        //извлекаем из джиры задачи
        $issues = $this->service->fetchDataFromJira($jql, $fetchedCount)->issues;
        dd($issues);

        $countIssues = count($issues);
        if ($countIssues > 0) {
            //Проверка ключа задач. Должен начинаться с HELP-, иначе всё накроется медным тазом...
            if ($this->checkProjectKeyName($issues) === false) {
                return false;
            }


            $result = $this->service->insertToDatabase($issues);
            if ($result) {
                $resultMsg = sprintf(
                    '[%s]. %d issues are inserted into the database. Total count in database - %d.',
                    Carbon::now()->toDateTimeString(),
                    $countIssues,
                    $this->service->getTotalIssuesInDb()
                );
            }
        }
        $this->info($resultMsg);
        return true;
    }

    /**
     * @param array $issues
     * @return bool
     */
    public function checkProjectKeyName(array $issues): bool
    {
        /** @var JiraIssue $issue */
        foreach ($issues as $issue) {
            if (strpos($issue->key, 'HELP-') !== 0) {
                $errorMsg = sprintf(
                    '[%s]. Stop parsing!!! Issues key does not match HELP- . id=%s, key=%s',
                    Carbon::now()->toDateTimeString(),
                    $issue->id,
                    $issue->key
                );
                $this->error($errorMsg);
                return false;
            }
        }
        return true;
    }
}
