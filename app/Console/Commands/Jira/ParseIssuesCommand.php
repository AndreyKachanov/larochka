<?php

namespace App\Console\Commands\Jira;

use App\Entity\Jira\Issue;
use App\Services\Jira\ParsingIssuesService;
use Illuminate\Console\Command;

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

        if ($fetchedCount <= 0 || $fetchedCount > 3000) {
            $this->error("Value fetchedCount not must be <= 0 || >= 3000");
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
            $lastIssueKey = Issue::orderByDesc('id')->first()->key;

            $jql = sprintf(
                'project = %s AND key > %s ORDER BY key ASC',
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
        $resultMsg = 'No new issues.';
        //извлекаем из джиры задачи
        $issues = $this->service->fetchDataFromJira($jql, $fetchedCount)->issues;

        if (count($issues) > 0) {
            $result = $this->service->insertToDatabase($issues);
            if ($result) {
                $resultMsg = sprintf(
                    '%d issues are inserted into the database. Total count in database - %d.',
                    $fetchedCount,
                    $this->service->getTotalIssuesInDb()
                );
            }
        }
        $this->info($resultMsg);
        return true;
    }
}
