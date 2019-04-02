<?php

//Broadcast::channel('room.{room_id}', function ($user, $room_id) {
//
//    if ($user->rooms->contains($room_id)) {
//        return $user->name;
//    }
//});

Broadcast::channel('room.{room_id}', function ($user, $room_id) {
    return (int)$user->id === (int)$room_id;
    //return $user->id === 3 || $user->id === 4;
    //return true;
});
