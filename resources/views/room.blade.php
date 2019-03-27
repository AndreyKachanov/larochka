@extends('layouts.app')

{{--@section('breadcrumbs', '')--}}

@section('content')
    <div class="container">
        <private-chat :room="{{$room}}" :user="{{Auth::user()}}"></private-chat>
    </div>
@endsection
