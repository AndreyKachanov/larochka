<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\Operator
 *
 * @property string $user_key
 * @property int $line_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Operator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Operator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Operator query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Operator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Operator whereLineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Operator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Operator whereUserKey($value)
 * @mixin \Eloquent
 * @property-read \App\Entity\Jira\Creator $creator
 * @property-read \App\Entity\Jira\Line $line
 * @property-read \App\Entity\Jira\Creator $rCreator
 * @property-read \App\Entity\Jira\Line $rLine
 */
class Operator extends Model
{
    public $incrementing = false;

    protected $table = 'jira_operators';

    protected $primaryKey = 'user_key';

    protected $guarded = ['user_key'];

    public function rLine()
    {
        return $this->belongsTo(Line::class, 'line_id', 'line_id');
    }

    public function rCreator()
    {
        return $this->belongsTo(Creator::class, 'user_key', 'user_key');
    }
}
