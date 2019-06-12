<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 07.06.19
 * Time: 0:13
 */

namespace App\Services\Jira;

use App\Entity\Jira\Component;
use App\Entity\Jira\Issue;
use App\Entity\Jira\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Issue as JiraIssue;
use Exception;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\Project\Component as JiraProjectComponent;

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
        //получаем компоненты с джиры
        $componentsJiraObj = $this->getComponentsFromJira();

        //подготовленный массив отсутствующих в бд пользователей
        $users = $this->convertUsers($issues);

        //подготовленный массив отсутствующих в бд компонентов
        $components = $this->convertComponents($componentsJiraObj);

        try {
            DB::transaction(function () use ($users, $components, $issues) {
                if (count($users) > 0) {
                    User::insert($users);
                }
                if (count($components) > 0) {
                    Component::insert($components);
                }
                /** @var JiraIssue $item */
                foreach ($issues as $item) {
                    //dd($item->fields->components);
                    //создаем сущность Issue и записываем в бд
                    $issue = new Issue();
                    $issue->jira_id = (int)$item->id;
                    $issue->key = $item->key;
                    $issue->summary = $item->fields->summary;
                    $issue->issue_type = $item->fields->issuetype->name;
                    $issue->creator = $item->fields->creator->name;
                    $issue->assignee = $item->fields->assignee->name ?? null;
                    $issue->status = $item->fields->status->name;
                    $issue->created_in_jira = Carbon::instance($item->fields->created)->addHours(3);
                    $issue->save();

                    $componentsFromJira = $item->fields->components;
                    if (count($componentsFromJira) > 0) {
                        $arrToSync = [];
                        /** @var JiraProjectComponent $comp */
                        foreach ($componentsFromJira as $comp) {
                            $arrToSync[] = $comp->id;
                        }

                        $issue->components()->sync($arrToSync);
                    }
                }
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
                'Error fetch issues from jira. %s.  Class - %s, line - %d',
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
                'avatar'       => $user->avatarUrls['48x48'],
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
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

    /**
     * @return array
     */
    private function getComponentsFromJira(): array
    {
        $projectName = config('jira.project_name');
        try {
            $projectInfoObject = new ProjectService();
            $project = $projectInfoObject->get($projectName);
        } catch (Exception $e) {
            $errorMsg = sprintf(
                'Error fetch project info from jira. %s.  Class - %s, line - %d',
                $e->getMessage(),
                __CLASS__,
                __LINE__
            );
            dd($errorMsg);
        }

        return $project->components;
    }

    /**
     * @param array $componentsJiraObj
     * @return array
     */
    private function convertComponents(array $componentsJiraObj): array
    {
        $components = [];
        $componentsFromDb = Component::pluck('jira_id')->toArray();

        /** @var JiraProjectComponent $compJr */
        foreach ($componentsJiraObj as $compJr) {
            if (in_array((int)$compJr->id, $componentsFromDb) === false) {
                $arr = [
                    'name'       => $compJr->name,
                    'jira_id'    => (int)$compJr->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $components[] = $arr;
            }
        }
        return $components;
    }
}
