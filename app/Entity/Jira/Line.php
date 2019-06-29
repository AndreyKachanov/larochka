<?php

namespace App\Entity\Jira;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Entity\Jira\Line
 *
 * @property int $line_id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Line newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Line newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Line query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Line whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Line whereLineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Line whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Jira\Line whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Operator[] $operators
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity\Jira\Operator[] $rOperators
 */
class Line extends Model
{
    public $incrementing = true;

    protected $table = 'jira_operator_lines';

    protected $primaryKey = 'line_id';

    protected $guarded = ['line_id'];

    public function rOperators()
    {
        return $this->hasMany(Operator::class, 'line_id', 'line_id');
    }
}
