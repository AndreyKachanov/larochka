<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\Creator
 *
 * @property string $user_key
 * @property string|null $display_name
 * @property string|null $email
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Creator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Creator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Creator query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Creator whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Creator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Creator whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Creator whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Creator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Creator whereUserKey($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Issue[] $issues
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Issue[] $rIssues
 * @property-read \App\Entity\Jira\Operator $rOperator
 * @property int $role_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Creator whereRoleId($value)
 */
class Creator extends Model
{
    public $incrementing = false;

    protected $table = 'jira_users';

    protected $primaryKey = 'user_key';

    public function rIssues()
    {
        return $this->hasMany(Issue::class, 'user_key', 'creator');
    }

    public function rOperator()
    {
        return $this->belongsTo(Operator::class, 'user_key', 'user_key');
    }
}
