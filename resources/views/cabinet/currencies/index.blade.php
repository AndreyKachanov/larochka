@php

@endphp

@extends('layouts.app')

@section('content')
    @include('cabinet.currencies._nav')

    <div class="currencies">
        <div class="card mb-3">
            <div class="card-header">Search for a favorable exchange rate</div>
            <div class="card-body">

                <currencies
                        route_start_parse="{{route('cabinet.start_parse')}}"
                        route_stop_parse="{{route('cabinet.stop_parse')}}"
                        :cache ="{{  json_encode($cache) }}"
                >
                </currencies>

                <currencies-table :user="{{Auth::user()}}"></currencies-table>
            </div>
        </div>
    </div>

@endsection