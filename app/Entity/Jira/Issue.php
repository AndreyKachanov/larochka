<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\Issue
 *
 * @property int $issue_id
 * @property int $key
 * @property string|null $summary
 * @property string|null $issue_type
 * @property string|null $status
 * @property string|null $resolution
 * @property string $creator
 * @property string|null $assignee
 * @property string|null $sender
 * @property string|null $sended_at
 * @property string $created_at_jira
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Component[] $components
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereAssignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereCreatedAtJira($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereIssueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereIssueType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereSendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Issue extends Model
{
    public $incrementing = false;

    protected $table = 'jira_issues';

    protected $primaryKey = 'issue_id';

    protected $guarded = ['issue_id'];

    protected $casts = [
        'created_in_jira' => 'datetime'
    ];

    public function components()
    {
        return $this->belongsToMany(Component::class, 'jira_component_issue', 'issue_id', 'component_id');
    }
}
