@php

@endphp

@extends('layouts.app')

@section('content')
    @include('cabinet.currencies._nav')

    <div class="currencies">
        <div class="card mb-3">
            <div class="card-header">Search for a favorable exchange rate</div>
            <div class="card-body">

                <parser
                        route_parse="{{route('cabinet.parse')}}"
                        :user="{{Auth::user()}}"
                >
                </parser>

                {{--<currencies-table :user="{{Auth::user()}}"></currencies-table>--}}
            </div>
        </div>
    </div>

@endsection