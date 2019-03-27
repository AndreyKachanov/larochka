<?php

namespace App\Entity\Chat;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'chat_messages';

    protected $guarded = ['id'];
}
