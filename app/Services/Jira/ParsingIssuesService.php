<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 07.06.19
 * Time: 0:13
 */

namespace App\Services\Jira;

use App\Entity\Jira\Issue;
use App\Entity\Jira\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Issue as JiraIssue;
use Exception;
use JiraRestApi\Issue\Reporter;

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
     * @return bool
     * @throws \Throwable
     */
    public function insertToDatabase(array $issues): bool
    {
        $users = $this->convertUsers($issues);

        /** @var JiraIssue $issue */
        foreach ($issues as $issue) {
            $issuesForDb[] = [
                'jira_id'    => $issue->id,
                'key'        => $issue->key,
                'summary'    => $issue->fields->summary,
                'issue_type' => $issue->fields->issuetype->name,
                'status'     => $issue->fields->status->name,
                'creator'    => $issue->fields->creator->name,
                'assignee'   => $issue->fields->assignee->name ?? null,
                'created_at' => Carbon::instance($issue->fields->created)->addHours(2),
            ];
        }

        if (isset($issuesForDb)) {
            if (count($issuesForDb) > 0) {
                try {
                    DB::transaction(function () use ($users, $issuesForDb) {
                        if (count($users) > 0) {
                            User::insert($users);
                        }
                        Issue::insert($issuesForDb);
                    }, 5);
                } catch (Exception $e) {
                    $errorMsg = sprintf(
                        'Error insert to database issues or users. %s.  Class - %s, line - %d',
                        $e->getMessage(),
                        __CLASS__,
                        __LINE__
                    );
                    dd($errorMsg);
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

    /**
     * @param array $issues
     * @return array
     */
    private function convertUsers(array $issues): array
    {
        $users = [];
        $allUsers = [];

        foreach ($issues as $issue) {
            if ($issue->fields->creator != null) {
                $allUsers[] = $issue->fields->creator;
            }

            if ($issue->fields->assignee != null) {
                $allUsers[] = $issue->fields->assignee;
            }
        }
        //удаляем дубликаты из массива объектов
        $withoutDuplicates = $this->removeDuplicateValues($allUsers);

        //перебор массива объектов - если в бд уже есть пользователя, исключаем его из массива
        $checkInDb = array_filter($withoutDuplicates, function (Reporter $obj) {
            return !$this->checkUserFromDb($obj->name);
        });

        //формируем массив пользователей
        /** @var Reporter $user */
        foreach ($checkInDb as $user) {
            $users[] = [
                'user_key'     => $user->name,
                'display_name' => $user->displayName,
                'email'        => $user->emailAddress,
                'avatar'       => $user->avatarUrls['48x48']
            ];
        }

        return $users;
    }

    /**
     * Checks the existence of a user in the database.
     * True - exists, false - not exists.
     *
     * @param string $userKey
     * @return bool
     */
    private function checkUserFromDb(string $userKey): bool
    {
        return User::whereUserKey($userKey)->count();
    }

    /**
     * Remove duplicate values in array of objects
     *
     * @param $array
     * @param bool $keepKeyAssoc
     * @return array
     */
    private function removeDuplicateValues($array, $keepKeyAssoc = false): array
    {
        $duplicateKey = [];
        $tmp = [];

        foreach ($array as $key => $val) {
            // convert objects to arrays, in_array() does not support objects
            if (is_object($val)) {
                $val = (array)$val;
            }

            if (!in_array($val, $tmp)) {
                $tmp[] = $val;
            } else {
                $duplicateKey[] = $key;
            }
        }

        foreach ($duplicateKey as $key) {
            unset($array[$key]);
        }

        return $keepKeyAssoc ? $array : array_values($array);
    }
}
