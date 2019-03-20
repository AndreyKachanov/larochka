@php
 /**@var \App\Entity\Email\Message $message*/
@endphp

<img style="width: 30px;" src="{{
asset("assets/images/flags/$message->country_code.svg") }}" alt="{{ $message->country_code }}" title="{{ $message->country_code }}">

