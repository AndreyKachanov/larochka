<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\Project
 *
 * @property int $project_id
 * @property string|null $key
 * @property string|null $name
 * @property string|null $avatar_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Issue[] $rIssues
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Project whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Project whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Project whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Project whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Project extends Model
{
    public $incrementing = false;

    protected $table = 'jira_projects';

    protected $primaryKey = 'project_id';

    //можно записывать все поля
    protected $guarded = [];

    public function rIssues()
    {
        return $this->hasMany(Issue::class, 'project_id', 'project_id');
    }
}
