@php

@endphp

@extends('layouts.app')

@section('content')
    @include('cabinet.jira._nav')
    @include('cabinet.jira._nav_jira')

    <chartline-component
            :data="{{ json_encode($data) }}"
    >
    </chartline-component>
@endsection