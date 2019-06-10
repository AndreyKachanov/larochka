<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\User
 *
 * @property int $id
 * @property string|null $user_key
 * @property string|null $display_name
 * @property string|null $email
 * @property string|null $avatar
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User whereUserKey($value)
 * @mixin \Eloquent
 */
class User extends Model
{
    public $timestamps = false;

    protected $table = 'jira_users';

    protected $guarded = ['id'];
}
