<?php

namespace App\Console\Commands\Jira;

use App\Entity\Jira\Creator;
use App\Entity\Jira\Issue;
use App\Entity\Jira\Operator;
use App\Entity\Jira\Project;
use App\Services\Jira\ParsingIssuesService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ParseAllProjectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:issues_all_projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parsing jira issues for all projects.';
    /**
     * @var ParsingIssuesService
     */
    private $service;

    /**
     * Create a new command instance.
     *
     * @return void
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
        //dd(Issue::whereCreator('dragliuk')->get());

        $fetchedCount = (int)config('jira.fetched_count_all_project');

        if ($fetchedCount <= 0 || $fetchedCount > 10000) {
            $this->error("Value fetchedCount not must be <= 0 || >= 10000");
            return false;
        }

        //dd(Creator::whereIn('role_id', [2, 3])->get());

        //если в бд нет пользователей с линии l1 и l2
        if (Creator::whereIn('role_id', [2, 3])->count() === 0) {
            $this->error("No found users with role l1 and l2.");
            return false;
        }

        if (Project::all()->count() === 0) {
            $this->error("Table jira_projects is empty.");
            return false;
        }

        //Извлекаем из джиры все проекты и записываем в бд
        $this->service->handleProjectsInfo();

        $fields = [
            //'*all',
            'summary',
            'issuetype',
            'creator',
            'assignee',
            'status',
            'resolution',
            'created',
            'project'
        ];

        $expand = [];

        //dd($operators);
        $issues = $this->service->fetchIssuesFromAllProjects($fetchedCount, $fields, $expand);
        //dd($issues);
        $countIssues = count($issues);
        //сортируем массив по полю creator
        //usort($issues, array($this->service, "cmp"));

        $resultMsg = '[' . Carbon::now()->toDateTimeString() . ']. ' . 'No new issues.';

        if ($countIssues > 0) {
            $result = $this->service->insertToDatabase($issues, $insertForAllProjects = true);
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
}
