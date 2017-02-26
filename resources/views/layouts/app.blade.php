<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ url('/img/favicon.png') }}" rel="icon" type="image/png">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <!--[if lt IE 9]>
        <style type="text/css">
            .upgrade-browser.danger { display: block; }
        </style>
        <script src="https://raw.githubusercontent.com/scottjehl/Respond/master/src/matchmedia.polyfill.js" type="text/javascript"></script>
        <script src="https://raw.githubusercontent.com/scottjehl/Respond/master/src/matchmedia.addListener.js" type="text/javascript"></script>
        <script src="https://raw.githubusercontent.com/scottjehl/Respond/master/src/respond.js" type="text/javascript"></script>
    <![endif]-->
</head>
<body>
    <div id="app">
        @include('includes.partials._upgrade_browser_danger')
        @if (Session::get('success_message'))
            @include('includes.partials._flash_success_message')
        @endif
        @if (Session::get('warning_message'))
            @include('includes.partials._flash_warning_message')
        @endif
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        @if (Auth::check())
                            @if (Auth::user()->isAdmin())
                                <li><a href="{{ route('admin.home') }}">Admin Dashboard</a></li>
                                <li><a href="{{ route('admin.export.index') }}">Export</a></li>
                            @else
                                @if (Auth::user()->hasTeam())
                                    <li><a href="{{ route('coach.team.manage', Auth::user()->team()->first()) }}">My Team</a></li>
                                @else
                                    <li><a href="{{ route('home') }}">Home</a></li>
                                @endif
                            @endif
                        @endif
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                            <li><a href="{{ url('/register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name() }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    @if (Auth::user()->isAdmin())
                                        <li class="dropdown-header">Admin</li>
                                        <li><a href="{{ route('admin.home') }}">Dashboard</a></li>
                                        <li><a href="{{ route('admin.export.index') }}">Export Data</a></li>
                                        <li role="separator" class="divider"></li>

                                        <li class="dropdown-header">Coach</li>
                                    @endif

                                    @if (Auth::user()->hasTeam())
                                        <li><a href="{{ route('coach.team.manage', Auth::user()->team()->first()) }}">Manage Team</a></li>
                                        <li><a href="{{ route('coach.rounds.index', Auth::user()->team()->first()) }}">Fill in Rounds</a></li>
                                        <li><a href="{{ route('coach.players.index', Auth::user()->team()->first()) }}">Add Players</a></li>
                                    @else
                                        <li><a href="{{ route('coach.teams.index') }}">Join a Team</a></li>
                                    @endif

                                    <li role="separator" class="divider"></li>
                                    <li class="dropdown-header">Support</li>

                                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                                    <li><a href="{{ 'mailto:' . env('ADMIN_SUPPORT_EMAIL_ADDRESS') }}">Contact Admin</a></li>

                                    <li role="separator" class="divider"></li>

                                    <li>
                                        <a href="{{ url('/logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
        @include('includes.partials._footer')
    </div>

    <!-- Scripts -->
    <script src="{{ elixir('js/app.js') }}"></script>
</body>
</html>
