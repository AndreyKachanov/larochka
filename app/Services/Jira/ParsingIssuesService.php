<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 07.06.19
 * Time: 0:13
 */

namespace App\Services\Jira;

use App\Entity\Jira\Issue;
use Illuminate\Support\Carbon;
use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Issue as JiraIssue;
use Exception;

class ParsingIssuesService
{
    /**
     * @param string $projectName
     * @return int
     */
    public function getTotalCountIssues(string $projectName): int
    {
        $jql = "project = " . $projectName;
        $startAt = 0;
        $maxResult = 1;

        $searchResult = $this->search(
            $jql,
            $startAt,
            $maxResult
        );
        return $searchResult->total;
    }

    /**
     * @param string $jql
     * @param int $fetchedCount
     * @return IssueSearchResult
     */
    public function fetchDataFromJira(string $jql, int $fetchedCount): IssueSearchResult
    {
        $startAt = 0;
        $maxResult = $fetchedCount;
        return $this->search(
            $jql,
            $startAt,
            $maxResult
        );
    }

    /**
     * @param array $issues
     * @return array
     */
    public function dataForDb(array $issues): array
    {
        $dataForDb = [];
        /** @var JiraIssue $issue */
        foreach ($issues as $issue) {
            $dataForDb[] = [
                'jira_id'    => $issue->id,
                'key'        => $issue->key,
                'summary'    => $issue->fields->summary,
                'issue_type' => $issue->fields->issuetype->name,
                'status'     => $issue->fields->status->name,
                'creator'    => $issue->fields->creator->displayName,
                'assignee'   => $issue->fields->assignee->displayName,
                'created_at' => Carbon::instance($issue->fields->created)->addHours(2),
            ];
        }
        return $dataForDb;
    }

    /**
     * @param array $dataFromDB
     * @return bool
     */
    public function insertToDatabase(array $dataFromDB): bool
    {
        if (isset($dataFromDB)) {
            if (count($dataFromDB) > 0) {
                try {
                    Issue::insert($dataFromDB);
                } catch (Exception $e) {
                    dd($e->getMessage());
                }
            }
        }

        return true;
    }

    /**
     * @return int
     */
    public function getTotalIssuesInDb(): int
    {
        return Issue::count();
    }

    /**
     * @param string $jql
     * @param int $startAt
     * @param int $maxResult
     * @return IssueSearchResult
     */
    private function search(string $jql, int $startAt, int $maxResult): IssueSearchResult
    {
        $fields = [
            //'*all',
            'summary',
            'issuetype',
            'creator',
            'assignee',
            'status',
            'components',
            'created'
        ];

        $expand = [
            'changelog'
        ];

        try {
            $issueService = new IssueService();
            $result = $issueService->search($jql, $startAt, $maxResult, $fields, $expand);
        } catch (Exception $e) {
            $errorMsg = sprintf(
                'Error while getting the day started. %s.  Class - %s, line - %d',
                $e->getMessage(),
                __CLASS__,
                __LINE__
            );
            dd($errorMsg);
        }
        return $result;
    }
}
