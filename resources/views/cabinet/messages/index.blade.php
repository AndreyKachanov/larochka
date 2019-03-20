@php
    /** @var \App\Entity\Email\Message $message */
    /** @var \App\Entity\Email\Message $sender */
    /** @var \App\Entity\Email\Message $email */
    /** @var \App\Entity\Email\Message $ip */
    /** @var \App\Entity\Email\Message $city */
    /** @var \App\Entity\Email\Message $region */
    /** @var \App\Entity\Email\Message $provider */
    /** @var \App\Entity\Email\Message $country */
    /** @var \App\Entity\Email\Message $mobile*/
    /** @var \Illuminate\Pagination\LengthAwarePaginator $messages */
    /** @var \Illuminate\Database\Eloquent\Collection $senders */
@endphp

@extends('layouts.app')

@section('content')
    @include('cabinet.messages._nav')

    <div class="card mb-3">
        <div class="card-header">Filter</div>
        <div class="card-body">
            <form action="?" method="GET">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="date_received" class="col-form-label">Date received</label>
                            <input id="date_received" class="form-control" name="date_received"
                                   value="{{ request('date_received') }}">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">

                            <label for="email" class="col-form-label">Email</label>
                            <select id="email" class="form-control" name="email">
                                <option value=""></option>
                                @isset($emails)
                                    @forelse ($emails as $key => $email)
                                        <option {{ $email->email === request('email') ? ' selected' : '' }}>{{ $email->email }}</option>
                                    @empty
                                        <option value="">No emails</option>
                                    @endforelse;
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label">Sender</label>
                            <select id="status" class="form-control" name="sender">
                                <option value=""></option>
                                @isset($senders)
                                    @forelse ($senders as $key => $sender)
                                        <option {{ $sender->sender === request('sender') ? ' selected' : '' }}>{{ $sender->sender }}</option>
                                    @empty
                                        <option value="">No senders</option>
                                    @endforelse
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="ip" class="col-form-label">Ip address</label>
                            <select id="ip" class="form-control" name="ip_address">
                                <option value=""></option>
                                @isset($ipAddress)
                                    @forelse ($ipAddress as $key => $ip)
                                        <option {{ $ip->ip_address === request('ip_address') ? ' selected' : '' }}>{{ $ip->ip_address }}</option>
                                    @empty
                                        <option value="">No ip adresses</option>
                                    @endforelse
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="isp" class="col-form-label">Provider</label>
                            <select id="isp" class="form-control" name="isp">
                                <option value=""></option>
                                @isset($providers)
                                    @forelse ($providers as $key => $provider)
                                        <option {{ $provider->isp === request('isp') ? ' selected' : '' }}>{{ $provider->isp }}</option>
                                    @empty
                                        <option value="">No providers</option>
                                    @endforelse
                                @endisset
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="mobile" class="col-form-label">Is mobile</label>
                            <select id="mobile" class="form-control" name="mobile">
                                <option value=""></option>
                                @if($isMobile->count() > 0)
                                    @forelse ($isMobile as $key => $mobile)
                                        <option value="{{ $mobile->mobile }}" {{ $mobile->mobile === (int)request('mobile') ? ' selected' : '' }}>{{ $mobile->mobile ? 'From mobile' : 'Not mobile' }}</option>
                                    @empty
                                        <option value="">No items</option>
                                    @endforelse
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="city" class="col-form-label">City</label>
                            <select id="city" class="form-control" name="city">
                                <option value=""></option>
                                @isset($cities)
                                    @forelse ($cities as $key => $city)
                                        <option {{ $city->city === request('city') ? ' selected' : '' }}>{{ $city->city }}</option>
                                    @empty
                                        <option value="">No cities</option>
                                    @endforelse
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="region" class="col-form-label">Region</label>
                            <select id="region" class="form-control" name="region_name">
                                <option value=""></option>
                                @isset($regions)
                                    @forelse ($regions as $key => $region)
                                        <option {{ $region->region_name === request('region_name') ? ' selected' : '' }}>{{ $region->region_name }}</option>
                                    @empty
                                        <option value="">No regions</option>
                                    @endforelse
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="country" class="col-form-label">Country</label>
                            <select id="country" class="form-control" name="country">
                                <option value=""></option>
                                @isset($countries)
                                    @forelse ($countries as $key => $country)
                                        <option {{ $country->country === request('country') ? ' selected' : '' }}>{{ $country->country }}</option>
                                    @empty
                                        <option value="">No Countries</option>
                                    @endforelse
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="col-form-label">&nbsp;</label><br/>
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-striped table-messages">
        <thead>
            <tr class="d-flex">
                <th class="col-1">Date</th>
                <th class="col-2">Email</th>
                <th class="col-2">Sender</th>
                <th class="col-2">Ip address</th>
                <th class="col-2">Provider</th>
                {{--<th class="col-1">Is mobile</th>--}}
                <th class="col-1">City</th>
                <th class="col-1">Region</th>
                <th class="col-1"></th>
            </tr>
        </thead>
        <tbody>

        @forelse($messages as $message)
            <tr class="d-flex">
                <td class="col-1">{{ $message->date_received->format('d.m.Y H:i:s') }}</td>
                <td class="col-2" style="word-wrap: break-word">{{ $message->email }}</td>
                <td class="col-2">{{ $message->sender }}</td>
                <td class="col-2">{{ $message->ip_address }}</td>
                <td class="col-2">{{ $message->isp }}</td>
                {{--<td class="col-1">--}}
                    {{--@if($message->mobile)--}}
                        {{--<img style="width: 30px" src="{{ asset("assets/images/ismobile.png") }}" alt="">--}}
                    {{--@endif--}}
                {{--</td>--}}
                <td class="col-1">{{ $message->city }}</td>
                <td class="col-1">{{ $message->region_name }}</td>
                <td class="col-1" align="right">
                    @includeWhen($message->country_code, 'cabinet.messages.flag')
                </td>
            </tr>
        @empty
            <p>No messages</p>
        @endforelse

        </tbody>
    </table>
    {{ $messages->appends(request()->except('page'))->links() }}

@endsection