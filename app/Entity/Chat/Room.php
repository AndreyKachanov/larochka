<?php

namespace App\Entity\Chat;

use App\Entity\User\User;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{

    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
