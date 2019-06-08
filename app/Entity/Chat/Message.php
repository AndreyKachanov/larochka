<?php

namespace App\Entity\Chat;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Chat\Message
 *
 * @property int $id
 * @property int $from
 * @property int $to
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Message whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Message whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Chat\Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    protected $table = 'chat_messages';

    protected $guarded = ['id'];
}
