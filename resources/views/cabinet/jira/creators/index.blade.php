@php
    /** @var \App\Entity\Jira\User $creator */
    /** @var \Illuminate\Pagination\LengthAwarePaginator $creators */
@endphp

@extends('layouts.app')

@section('content')
    @include('cabinet.jira._nav')
    @include('cabinet.jira._nav_jira')

    {{--<div class="card mb-3">--}}
        {{--<div class="card-header">Filter</div>--}}
        {{--<div class="card-body">--}}
            {{--<form action="?" method="GET">--}}
                {{--<div class="row">--}}
                    {{--<div class="col-sm-2">--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="user_key" class="col-form-label">User key</label>--}}
                            {{--<input id="user_key" class="form-control" name="user_key"--}}
                                   {{--value="{{ request('user_key') }}">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-2">--}}
                        {{--<div class="form-group">--}}

                            {{--<label for="email" class="col-form-label">Email</label>--}}
                            {{--<select id="email" class="form-control" name="email">--}}
                                {{--<option value=""></option>--}}
                                {{--@isset($emails)--}}
                                    {{--@forelse ($emails as $key => $email)--}}
                                        {{--<option {{ $email->email === request('email') ? ' selected' : '' }}>{{ $email->email }}</option>--}}
                                    {{--@empty--}}
                                        {{--<option value="">No emails</option>--}}
                                    {{--@endforelse;--}}
                                {{--@endisset--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-3">--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="status" class="col-form-label">Sender</label>--}}
                            {{--<select id="status" class="form-control" name="sender">--}}
                                {{--<option value=""></option>--}}
                                {{--@isset($senders)--}}
                                    {{--@forelse ($senders as $key => $sender)--}}
                                        {{--<option {{ $sender->sender === request('sender') ? ' selected' : '' }}>{{ $sender->sender }}</option>--}}
                                    {{--@empty--}}
                                        {{--<option value="">No senders</option>--}}
                                    {{--@endforelse--}}
                                {{--@endisset--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-2">--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="ip" class="col-form-label">Ip address</label>--}}
                            {{--<select id="ip" class="form-control" name="ip_address">--}}
                                {{--<option value=""></option>--}}
                                {{--@isset($ipAddress)--}}
                                    {{--@forelse ($ipAddress as $key => $ip)--}}
                                        {{--<option {{ $ip->ip_address === request('ip_address') ? ' selected' : '' }}>{{ $ip->ip_address }}</option>--}}
                                    {{--@empty--}}
                                        {{--<option value="">No ip adresses</option>--}}
                                    {{--@endforelse--}}
                                {{--@endisset--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-3">--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="isp" class="col-form-label">Provider</label>--}}
                            {{--<select id="isp" class="form-control" name="isp">--}}
                                {{--<option value=""></option>--}}
                                {{--@isset($providers)--}}
                                    {{--@forelse ($providers as $key => $provider)--}}
                                        {{--<option {{ $provider->isp === request('isp') ? ' selected' : '' }}>{{ $provider->isp }}</option>--}}
                                    {{--@empty--}}
                                        {{--<option value="">No providers</option>--}}
                                    {{--@endforelse--}}
                                {{--@endisset--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="row">--}}
                    {{--<div class="col-sm-2">--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="mobile" class="col-form-label">Is mobile</label>--}}
                            {{--<select id="mobile" class="form-control" name="mobile">--}}
                                {{--<option value=""></option>--}}
                                {{--@if($isMobile->count() > 0)--}}
                                    {{--@forelse ($isMobile as $key => $mobile)--}}
                                        {{--<option value="{{ $mobile->mobile }}" {{ $mobile->mobile === (int)request('mobile') ? ' selected' : '' }}>{{ $mobile->mobile ? 'From mobile' : 'Not mobile' }}</option>--}}
                                    {{--@empty--}}
                                        {{--<option value="">No items</option>--}}
                                    {{--@endforelse--}}
                                {{--@endif--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-2">--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="city" class="col-form-label">City</label>--}}
                            {{--<select id="city" class="form-control" name="city">--}}
                                {{--<option value=""></option>--}}
                                {{--@isset($cities)--}}
                                    {{--@forelse ($cities as $key => $city)--}}
                                        {{--<option {{ $city->city === request('city') ? ' selected' : '' }}>{{ $city->city }}</option>--}}
                                    {{--@empty--}}
                                        {{--<option value="">No cities</option>--}}
                                    {{--@endforelse--}}
                                {{--@endisset--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-3">--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="region" class="col-form-label">Region</label>--}}
                            {{--<select id="region" class="form-control" name="region_name">--}}
                                {{--<option value=""></option>--}}
                                {{--@isset($regions)--}}
                                    {{--@forelse ($regions as $key => $region)--}}
                                        {{--<option {{ $region->region_name === request('region_name') ? ' selected' : '' }}>{{ $region->region_name }}</option>--}}
                                    {{--@empty--}}
                                        {{--<option value="">No regions</option>--}}
                                    {{--@endforelse--}}
                                {{--@endisset--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-2">--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="country" class="col-form-label">Country</label>--}}
                            {{--<select id="country" class="form-control" name="country">--}}
                                {{--<option value=""></option>--}}
                                {{--@isset($countries)--}}
                                    {{--@forelse ($countries as $key => $country)--}}
                                        {{--<option {{ $country->country === request('country') ? ' selected' : '' }}>{{ $country->country }}</option>--}}
                                    {{--@empty--}}
                                        {{--<option value="">No Countries</option>--}}
                                    {{--@endforelse--}}
                                {{--@endisset--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-3">--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="col-form-label">&nbsp;</label><br/>--}}
                            {{--<button type="submit" class="btn btn-primary">Search</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</form>--}}
        {{--</div>--}}
    {{--</div>--}}

    <table class="table table-striped table-messages">
        <thead>
            <tr class="d-flex">
                <th class="col-3">User key</th>
                <th class="col-3">Display name</th>
                <th class="col-3">Email</th>
                <th class="col-1"></th>
            </tr>
        </thead>
        <tbody>

        @forelse($creators as $creator)
            <tr class="d-flex">
                <td class="col-3">{{ $creator->user_key }}</td>
                <td class="col-3" style="word-wrap: break-word">{{ $creator->display_name }}</td>
                <td class="col-3">{{ $creator->email }}</td>
                <td class="col-1">
                    <img style="width: 30px" src="{{ $creator->avatar }}" alt="">
                </td>
            </tr>
        @empty
            <p>No creators</p>
        @endforelse

        </tbody>
    </table>
{{--    {{ $messages->appends(request()->except('page'))->links() }}--}}
    {{ $creators->links() }}

@endsection