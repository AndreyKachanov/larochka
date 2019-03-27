@extends('layouts.app')

@section('content')
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" href="{{ route('cabinet.home') }}">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('cabinet.messages.index') }}">Parsing</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('cabinet.posts.index') }}">Currency exchange</a></li>
        {{--<li class="nav-item"><a class="nav-link" href="{{ route('cabinet.banners.index') }}">Banners</a></li>--}}
        <li class="nav-item"><a class="nav-link" href="{{ route('cabinet.profile.home') }}">Profile</a></li>
        {{--<li class="nav-item"><a class="nav-link" href="{{ route('cabinet.tickets.index') }}">Tickets</a></li>--}}
    </ul>
@endsection