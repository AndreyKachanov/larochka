<?php

//Broadcast::channel('room.{room_id}', function ($user, $room_id) {
//
//    if ($user->rooms->contains($room_id)) {
//        return $user->name;
//    }
//});

Broadcast::channel('test', function ($user) {

    return $user->id === 3;
    //return false;
});
