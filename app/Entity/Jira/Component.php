<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\Component
 *
 * @property int $component_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Issue[] $jira
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component whereComponentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Issue[] $issues
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Issue[] $rIssues
 */
class Component extends Model
{
    public $incrementing = false;

    protected $table = 'jira_components';

    protected $primaryKey = 'component_id';

    protected $guarded = ['component_id'];

    public function rIssues()
    {
        return $this->belongsToMany(Issue::class, 'jira_component_issue', 'component_id', 'issue_id');
    }
}
