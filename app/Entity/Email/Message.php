<?php

namespace App\Entity\Email;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Entity\Email\Message
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $date_received
 * @property string|null $sender
 * @property string|null $email
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereDateReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $folder_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereFolderId($value)
 * @property string|null $folder_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereFolderName($value)
 * @property string|null $city
 * @property string|null $country_code
 * @property string|null $isp
 * @property int|null $mobile
 * @property string|null $org
 * @property string|null $region_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereIsp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereOrg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereRegionName($value)
 * @property string|null $country
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity\Email\Message whereCountry($value)
 */
class Message extends Model
{
    public $timestamps = false;

    protected $table = 'messages';

    protected $guarded = ['id'];

    protected $casts = [
        'date_received' => 'datetime'
    ];
}
