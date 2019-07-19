<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    @yield('meta')
    <!-- Styles -->
    <link href="{{ mix('css/app.css', (config('app.env') == 'local') ? 'build' : '' ) }}" rel="stylesheet">

</head>
<body>
    <div id="app">
        <header>
            <nav class="navbar navbar-expand-md navbar-dark">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.author') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav mr-auto">

                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Authentication Links -->
                            @guest
                                <li><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                                <li><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ Auth::user()->name }} <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        @can ('admin-panel')
                                            <a class="dropdown-item" href="{{ route('admin.home') }}">Admin</a>
                                        @endcan
                                        <a class="dropdown-item" href="{{ route('cabinet.home') }}">Cabinet</a>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            @section('search')
                {{--Search block--}}
{{--                @include('layouts.partials.search')--}}
            @show
        </header>

        <main class="app-content py-3">
            <div class="container">
                @section('breadcrumbs', Breadcrumbs::render())
                @yield('breadcrumbs')
                @include('layouts.partials.flash')
                @yield('content')
            </div>
        </main>

        <footer>
            <div class="container">
                <div class="border-top pt-3">
                    <p style="text-align: center;">&copy; {{ date('Y') }} - {{ config('app.author') }}</p>
                </div>
            </div>
        </footer>

    </div>

    <!-- Scripts -->
    <script  src="{{ mix('js/app.js', (config('app.env') == 'local') ? 'build' : '') }}" defer></script>
    @yield('scripts')
</body>
</html>
