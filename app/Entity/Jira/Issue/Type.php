<?php

namespace App\Entity\Jira\Issue;

use App\Entity\Jira\Issue;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\Issue\Type
 *
 * @property int $id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue\Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue\Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue\Type query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue\Type whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue\Type whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue\Type whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Issue\Type whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Issue[] $rIssues
 */
class Type extends Model
{
    protected $table = 'jira_issue_types';

    protected $guarded = ['id'];

    public function rIssues()
    {
        return $this->hasMany(Issue::class, 'id', 'issue_type_id');
    }
}
