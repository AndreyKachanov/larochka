<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\Component
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Issue[] $issues
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property int $jira_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component whereJiraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Component whereUpdatedAt($value)
 */
class Component extends Model
{
    protected $table = 'jira_components';
    protected $primaryKey = 'jira_id';

    protected $fillable = ['*'];

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'jira_component_issue', 'jira_id', 'id');
    }
}
