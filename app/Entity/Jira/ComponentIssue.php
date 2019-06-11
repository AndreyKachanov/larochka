<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\ComponentIssue
 *
 * @property int $issue_id
 * @property int $component_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\ComponentIssue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\ComponentIssue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\ComponentIssue query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\ComponentIssue whereComponentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\ComponentIssue whereIssueId($value)
 * @mixin \Eloquent
 */
class ComponentIssue extends Model
{
    public $timestamps = false;

    protected $table = 'component_issue';
}
