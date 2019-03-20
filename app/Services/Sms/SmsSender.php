<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 07.01.19
 * Time: 22:31
 */

namespace App\Services\Sms;

interface SmsSender
{
    public function send($number, $text): void;
}
