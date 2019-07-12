<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\User
 *
 * @property string $user_key
 * @property string|null $display_name
 * @property string|null $email
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereUserKey($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Issue[] $issues
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Issue[] $rIssues
 * @property-read \App\Entity\Jira\Operator $rOperator
 * @property int $role_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereRoleId($value)
 */
class User extends Model
{
    public $incrementing = false;

    protected $table = 'jira_users';

    protected $primaryKey = 'user_key';

    public function rIssues()
    {
        return $this->hasMany(Issue::class, 'creator', 'user_key');
    }
}
