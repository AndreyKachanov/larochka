<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\Issue
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $jira_id
 * @property string|null $key
 * @property string|null $summary
 * @property string|null $court
 * @property string|null $issuetype
 * @property string|null $author
 * @property string|null $assignee
 * @property string|null $status
 * @property string|null $components
 * @property \Illuminate\Support\Carbon|null $date_created
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereAssignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereComponents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereCourt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereIssuetype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereJiraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereSummary($value)
 * @property string|null $issue_type
 * @property string|null $creator
 * @property string|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereIssueType($value)
 * @property string|null $created_in_jira
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereCreatedInJira($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereUpdatedAt($value)
 */
class Issue extends Model
{
    protected $table = 'jira_issues';

    protected $guarded = ['id'];

    protected $casts = [
        'created_in_jira' => 'datetime'
    ];

    public function components()
    {
        return $this->belongsToMany(Component::class, 'component_issue');
    }
}
