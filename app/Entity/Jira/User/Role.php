<?php

namespace App\Entity\Jira\User;

use App\Entity\Jira\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\User\Role
 *
 * @property int $role_id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\User[] $rUsers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User\Role whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\User\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends Model
{
    protected $table = 'jira_users_role';

    protected $guarded = ['role_id'];

    protected $primaryKey = 'role_id';

    public function rUsers()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
