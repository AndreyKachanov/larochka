@php

@endphp

@extends('layouts.app')

@section('content')
    @include('cabinet.jira._nav')
    {{--@include('cabinet.jira._nav_jira')--}}

    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">HelpDesc (Виконавець)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">L1 => L2</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Всі проекти (Создатель)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-operators" role="tab" aria-controls="pills-contact" aria-selected="false">Оператори</a>
        </li>
        <li class="nav-item">
            <a class="nav-link red btn btn-info disabled" id="pills-contact-tab" data-toggle="pill" href="#pills-all" role="tab" aria-controls="pills-contact" aria-selected="false" href="#">Статистика за весь час</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <chartline-component
                    :data="{{ json_encode($data) }}"
            >
            </chartline-component>
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">В разработке</div>
        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">В разработке</div>
        <div class="tab-pane fade" id="pills-operators" role="tabpanel" aria-labelledby="pills-contact-tab">В разработке</div>
        <div class="tab-pane fade" id="pills-all" role="tabpanel" aria-labelledby="pills-contact-tab">В разработке</div>
    </div>


@endsection