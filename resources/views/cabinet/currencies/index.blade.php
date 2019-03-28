@php

@endphp

@extends('layouts.app')

@section('content')
    @include('cabinet.currencies._nav')

    <div class="card mb-3">
        <div class="card-header">Search for a favorable exchange rate</div>
        <div class="card-body">
            <currencies></currencies>
        </div>

        {{--<table class="table table-striped table-messages">--}}
            {{--<thead>--}}
            {{--<tr class="d-flex">--}}
                {{--<th class="col-1">Date</th>--}}
                {{--<th class="col-2">Post</th>--}}
                {{--<th class="col-2">Author</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}
            {{--<tbody>--}}
            {{----}}
            {{--</tbody>--}}
        {{--</table>--}}

    </div>




@endsection