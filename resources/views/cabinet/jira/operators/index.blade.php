@php
    /** @var \App\Entity\Jira\User $user */
    /** @var \Illuminate\Database\Eloquent\Collection $line1 */
    /** @var \Illuminate\Database\Eloquent\Collection $line2 */
@endphp

@extends('layouts.app')

@section('content')
    @include('cabinet.jira._nav')
    @include('cabinet.jira._nav_jira')
    <div class="operators">
        <p class="h5">Операторы L1</p>
        <p><a href="#" class="btn btn-success btn-sm">Добавить</a></p>
        @isset($line1)
            <table class="table table-hover line1">
                <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"></th>
                    <th scope="col">User key</th>
                    <th scope="col">Display name</th>
                </tr>
                </thead>
                <tbody>

                @forelse($line1 as $user)
                    <tr>
                        <th scope="row"></th>
                        <td>
                            <img style="width: 30px" src="{{ $user->avatar }}" alt="{{ $user->display_name }}" title="{{ $user->display_name }}">
                        <td>{{ $user->user_key }}</td>
                        <td>{{ $user->display_name }}</td>
                    </tr>
                @empty
                    <p>No operators l1</p>
                @endforelse
                </tbody>
            </table>
        @endisset

        <p class="h5">Операторы L2</p>
        <p><a href="#" class="btn btn-success btn-sm">Добавить</a></p>
        @isset($line2)
            <table class="table table-hover line2">
                <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"></th>
                    <th scope="col">User key</th>
                    <th scope="col">Display name</th>
                </tr>
                </thead>
                <tbody>

                @forelse($line2 as $user)
                    <tr>
                        <th scope="row"></th>
                        <td>
                            <img style="width: 30px" src="{{ $user->avatar }}" alt="{{ $user->display_name }}" title="{{ $user->display_name }}">
                        </td>
                        <td>{{ $user->user_key }}</td>
                        <td>{{ $user->display_name }}</td>
                    </tr>
                @empty
                    <p>No operators l1</p>
                @endforelse
                </tbody>
            </table>
        @endisset
    </div>
@endsection