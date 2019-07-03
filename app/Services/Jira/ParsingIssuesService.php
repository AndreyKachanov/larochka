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
use App\Entity\Jira\Creator;
use App\Entity\Jira\Operator;
use App\Entity\Jira\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use JiraRestApi\Issue\ChangeLog;
use JiraRestApi\Issue\History;
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
    public function fetchDataFromJira(string $jql, int $fetchedCount, array $fields, array $expand): IssueSearchResult
    {
        $startAt = 0;
        $maxResult = $fetchedCount;

        return $this->search(
            $jql,
            $startAt,
            $maxResult,
            $fields,
            $expand
        );
    }

    /**
     * @param array $issues
     * @param bool $insertForAllProjects
     * @return bool
     * @throws \Throwable
     */
    public function insertToDatabase(array $issues, bool $insertForAllProjects = false): bool
    {
        //если передан флаг что нужно записывать компоненты в бд
        //получаем компоненты с джиры
        if ($insertForAllProjects === false) {
            $componentsJiraObj = $this->getComponentsFromJira();
            //подготовленный массив отсутствующих в бд компонентов
            $components = $this->convertComponents($componentsJiraObj);
        } else {
            $components = [];
        }

        //подготовленный массив отсутствующих в бд пользователей
        $users = $this->convertUsers($issues, $insertForAllProjects);
        //dd($users);

        try {
            DB::transaction(function () use ($users, $components, $issues, $insertForAllProjects) {
                if (count($users) > 0) {
                    //dump($users);
                    Creator::insert($users);
                }

                if (count($components) > 0) {
                    Component::insert($components);
                }

                /** @var JiraIssue $item */
                foreach ($issues as $item) {
                    //dd($item);
                    //создаем сущность Issue и записываем в бд
                    $issue = new Issue();
                    $issue->issue_id = (int)$item->id;
                    $issue->project_id = (int)$item->fields->project->id;
                    $issue->key = ($item->key === null) ? null : $this->getCutKey($item->key);
                    $issue->summary = $item->fields->summary;
                    $issue->issue_type = $item->fields->issuetype->name;
                    $issue->status = $item->fields->status->name;
                    $issue->resolution = $item->fields->resolution->name ?? null;
                    $issue->creator = $item->fields->creator->name;
                    $issue->assignee = $item->fields->assignee->name ?? null;
                    $issue->sender = ($item->changelog === null)
                        ? null
                        : $this->getSenderInfo($item->changelog)['sender'];
                    $issue->sended_at = ($item->changelog === null)
                        ? null
                        : $this->getSenderInfo($item->changelog)['sended_at'];
                    $issue->created_at_jira = Carbon::instance($item->fields->created)->addHours(3);

                    //dd($issue);
                    $issue->save();

                    //если передан флаг что нужно записывать компоненты в бд
                    if ($insertForAllProjects === false) {
                        $componentsFromJira = $item->fields->components;
                        if (count($componentsFromJira) > 0) {
                            $arrToSync = [];
                            /** @var JiraProjectComponent $comp */
                            foreach ($componentsFromJira as $comp) {
                                $arrToSync[] = (int)$comp->id;
                            }
                            //dd($arrToSync);
                            $issue->rComponents()->sync($arrToSync);
                        }
                    }
                }
            }, 5);
        } catch (Exception $e) {
            $errorMsg = sprintf(
                '[%s]. Error insert to database jira or users. %s.  Class - %s, line - %d',
                Carbon::now()->toDateTimeString(),
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
     * @param Collection $operators
     * @param int $fetchedCount
     * @return array
     */
    public function fetchIssuesFromAllProjects(int $fetchedCount, array $fields = [], array $expand = []): array
    {
        $issues = [];

        //извлекаем проекты из бд кроме HelpDesc (исключаем HelpDesk проект)
        $projectFromDb = Project::orderBy('project_id')
            ->get()
            ->filter(function ($project) {
                /** @var Project $project */
                return $project->project_id != '10400';
            });

        //извлекаем всех операторов в бд
        //$operators = Operator::orderBy('user_key')->get();
        $operators = Creator::whereIn('role_id', [2, 3])
            ->orderByDesc('role_id')
            ->orderBy('user_key')
            ->get();

        //запускаем цикл по операторам
        /** @var Operator $operator */
        foreach ($operators as $operator) {
            //запускаем цикл по проектам
            /** @var Project $project */
            foreach ($projectFromDb as $project) {
                //dump($project);
                //ищет в бд issue с наибольшим key, которым относится в первому проекту в массиве
                $lastIssue = Issue::whereProjectId($project->project_id)
                    ->orderByDesc('key')
                    ->first();
                //dd($lastIssue);

                //если в бд есть issue с наибольшим key, который относится в первому проекту в массиве - извлекаем
                //по нему задачи
                if ($lastIssue !== null) {
                    $jql = sprintf(
                        "project = '%s' AND creator = %s AND key > %s ORDER BY key ASC",
                        $project->name,
                        $operator->user_key,
                        $lastIssue->rProject->key . '-' . $lastIssue->key
                    );
                    //dd($jql);

                    $resultArray = $this->getIssuesByProject($jql, $fetchedCount, $fields, $expand);
                    if (count($resultArray) > 0) {
                        $issues = array_merge($issues, $resultArray);
                    }

                    //если в бд нет issue относящихся к проекту - извлекаем самые первые задачи в джире
                } else {
                    //dd("else");
                    $jql = sprintf(
                        "project = '%s' and creator = %s ORDER BY key ASC",
                        $project->name,
                        $operator->user_key
                    );

                    $resultArray = $this->getIssuesByProject($jql, $fetchedCount, $fields, $expand);
                    if (count($resultArray) > 0) {
                        $issues = array_merge($issues, $resultArray);
                    }
                }
            }
        }
        //dd($issues);
        return $issues;
    }

    /**
     * @throws \Throwable
     */
    public function handleProjectsInfo(): void
    {
        try {
            $proj = new ProjectService();
            $projectsFromJira = $proj->getAllProjects();
        } catch (Exception $e) {
            $errorMsg = sprintf(
                '[%s]. Error fetch all projects from jira. %s.  Class - %s, line - %d',
                Carbon::now()->toDateTimeString(),
                $e->getMessage(),
                __CLASS__,
                __LINE__
            );
            dd($errorMsg);
        }

        if (count($projectsFromJira) === 0) {
            $errorMsg = sprintf(
                '[%s]. Count projects = 0. Class - %s, line - %d',
                Carbon::now()->toDateTimeString(),
                __CLASS__,
                __LINE__
            );
            dd($errorMsg);
        }

        /** @var \JiraRestApi\Project\Project $project */
        foreach ($projectsFromJira as $project) {
            if (Project::whereProjectId($project->id)->count() == 0) {
                try {
                    DB::transaction(function () use ($project) {
                        Project::create([
                            'project_id' => (int)$project->id,
                            'key'        => $project->key,
                            'name'       => $project->name,
                            'avatar_url' => $project->avatarUrls['48x48'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }, 5);
                } catch (Exception $e) {
                    $errorMsg = sprintf(
                        '[%s]. Error insert project to database. %s.  Class - %s, line - %d',
                        Carbon::now()->toDateTimeString(),
                        $e->getMessage(),
                        __CLASS__,
                        __LINE__
                    );
                    dd($errorMsg);
                }
            }
        }
    }

    /**
     * @param string $jql
     * @param int $startAt
     * @param int $maxResult
     * @param array $fields
     * @param array $expand
     * @return IssueSearchResult
     */
    private function search(
        string $jql,
        int $startAt,
        int $maxResult,
        array $fields = [],
        array $expand = []
    ): IssueSearchResult {
        try {
            $issueService = new IssueService();
            $result = $issueService->search($jql, $startAt, $maxResult, $fields, $expand);
        } catch (Exception $e) {
            $errorMsg = sprintf(
                '[%s]. Error fetch issues from jira. %s.  Class - %s, line - %d',
                Carbon::now()->toDateTimeString(),
                $e->getMessage(),
                __CLASS__,
                __LINE__
            );
            dd($errorMsg);
        }
        return $result;
    }

    /**
     * @param string $jql
     * @param int $fetchedCount
     * @param array $fields
     * @param array $expand
     * @return array
     */
    private function issuesToCommonArray(string $jql, int $fetchedCount, array $fields, array $expand): array
    {
        $issues = [];
        $resultArray = $this->getIssuesByProject($jql, $fetchedCount, $fields, $expand);
        if (count($resultArray) > 0) {
            foreach ($resultArray as $item) {
                $issues[] = $item;
            }
        }
        return $issues;
    }

    /**
     * @param string $jql
     * @param int $fetchedCount
     * @param array $fields
     * @param array $expand
     * @return array
     */
    private function getIssuesByProject(
        string $jql,
        int $fetchedCount,
        array $fields,
        array $expand
    ): array {
        return $this->fetchDataFromJira($jql, $fetchedCount, $fields, $expand)->issues;
    }

    /**
     * @param array $issues
     * @return array
     */
    private function convertUsers(array $issues, bool $insertForAllProjects): array
    {
        $users = [];
        $allUsers = [];

        /** @var JiraIssue $issue */
        foreach ($issues as $issue) {
            //dd($issue);
            if ($issue->fields->creator != null) {
                $allUsers[] = $issue->fields->creator;
            }

            if ($issue->fields->assignee != null) {
                $allUsers[] = $issue->fields->assignee;
            }

            if ($issue->changelog !== null) {
                if ($issue->changelog->total > 0) {
                    $usersFromChangelog = $this->getUsersFromChangelog($issue->changelog);
                    $allUsers = array_merge($allUsers, $usersFromChangelog);
                }
            }

            //dd($allUsers);
        }
        //dd($allUsers);
        //удаляем дубликаты из массива объектов
        $withoutDuplicates = $this->removeDuplicateValues($allUsers);

        //dd($withoutDuplicates);
        //перебор массива объектов - если в бд уже есть пользователя, исключаем его из массива
        //если передан флаг, что обрабатываются все проекты
        if ($insertForAllProjects === true) {
            $checkInDb = array_filter($withoutDuplicates, function (Reporter $obj) {

                return $this->checkUserFromDbForAllProjects($obj->name);
            });
        } else { //иначе обрабатываем проект HelpDesk
            $checkInDb = array_filter($withoutDuplicates, function (Reporter $obj) {
                return !$this->checkUserFromDb($obj->name);
            });
        }


        //формируем массив пользователей
        /** @var Reporter $user */
        foreach ($checkInDb as $user) {
            $users[] = [
                'user_key'     => $user->name,
                'role_id' => ($insertForAllProjects) ? 4 : null,
                'display_name' => $user->displayName,
                'email'        => $user->emailAddress,
                'avatar'       => $user->avatarUrls['48x48'],
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ];
        }
        //dd($users);
        return $users;
    }

    private function checkUserFromDbForAllProjects(string $userKey)
    {

        //если в бд есть пользователь с ролью 1 - обновляем ему role_id = 4 (операторов l1 и l2 не трогает)
        //так же исключаем его из массива, т.е. не возвращаем его
        if (Creator::whereUserKey($userKey)->whereRoleId(1)->count() > 0) {
            Creator::whereUserKey($userKey)->update(['role_id' => 4]);
            return false;
        }

        //если в бд уже есть user_key - не возвращаем его
        if (Creator::whereUserKey($userKey)->count() > 0) {
            return false;
        }

        return true;
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
        return Creator::whereUserKey($userKey)->count();
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
        $components = [];
        $projectName = config('jira.project_name');
        try {
            $projectInfoObject = new ProjectService();
            $components = $projectInfoObject->get($projectName)->components;
        } catch (Exception $e) {
            $errorMsg = sprintf(
                '[%s]. Error fetch project info from jira. %s.  Class - %s, line - %d',
                Carbon::now()->toDateTimeString(),
                $e->getMessage(),
                __CLASS__,
                __LINE__
            );
            dd($errorMsg);
        }

        if (is_null($components)) {
            $errorMsg = sprintf(
                '[%s]. Error fetching components project. $components is_null. Class - %s, line - %d',
                Carbon::now()->toDateTimeString(),
                __CLASS__,
                __LINE__
            );
            dd($errorMsg);
        }

        if (count($components) === 0) {
            $errorMsg = sprintf(
                '[%s]. Error fetching components project. Count = 0. Class - %s, line - %d',
                Carbon::now()->toDateTimeString(),
                __CLASS__,
                __LINE__
            );
            dd($errorMsg);
        }

        return $components;
    }

    /**
     * @param array $componentsJiraObj
     * @return array
     */
    private function convertComponents(array $componentsJiraObj): array
    {
        $components = [];
        $componentsFromDb = Component::pluck('component_id')->toArray();

        /** @var JiraProjectComponent $compJr */
        foreach ($componentsJiraObj as $compJr) {
            if (in_array((int)$compJr->id, $componentsFromDb) === false) {
                $arr = [
                    'name'         => $compJr->name,
                    'component_id' => (int)$compJr->id,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now()
                ];
                $components[] = $arr;
            }
        }
        return $components;
    }

    /**
     * @param string $item
     * @return int
     */
    private function getCutKey(string $item): int
    {
        return (int)explode('-', $item)[1];
    }

    /**
     * @param ChangeLog $changeLog
     * @return array
     */
    private function getSenderInfo(ChangeLog $changeLog): array
    {
        $arr = [
            'sender'    => null,
            'sended_at' => null
        ];
        if ($changeLog->total > 0) {
            /** @var History $history */
            foreach ($changeLog->histories as $history) {
                foreach ($history->items as $item) {
                    if (isset($item->toString)) {
                        if ($item->toString === "Черга для L2") {
                            return [
                                'sender'    => $history->author->name,
                                'sended_at' => Carbon::parse($history->created)->setTimezone('UTC')->addHours(2)
                            ];
                        }
                    }
                }
            }
        }

        return $arr;
    }

    /**
     * @param ChangeLog $changeLog
     * @return array
     */
    private function getUsersFromChangelog(ChangeLog $changeLog): array
    {
        $users = [];
        /** @var History $history */
        foreach ($changeLog->histories as $history) {
            $users[] = $history->author;
        }

        return $users;
    }


    /**
     * @param JiraIssue $a
     * @param JiraIssue $b
     * @return int
     */
    public function cmp(\JiraRestApi\Issue\Issue $a, \JiraRestApi\Issue\Issue $b)
    {
        return strcmp($a->fields->creator->name, $b->fields->creator->name);
    }
}
