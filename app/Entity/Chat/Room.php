<?php

namespace App\Entity\Chat;

use App\Entity\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Chat\Room
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\User\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Room query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Room whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Room extends Model
{

    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
