<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="SMAA Coach's Helper - The new way to track your team's data.">

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
    <body class="with-colour">
        <div id="app">
            @include('includes.partials._upgrade_browser_danger')
            <div class="jumbotron">
                <div class="container-fluid">
                    <h1>{{ config('app.name', 'Laravel') }}</h1>
                    <p>The new way to track your Team!</p>
                    <p><a href="{{ url('/login') }}" class="btn btn-primary btn-lg">Login</a> &nbsp; <a href="{{ url('/register') }}" class="btn btn-primary btn-lg">Sign Up!</a></p>
                </div>
            </div>
        </div>
        <!-- Scripts -->
        <script src="{{ elixir('js/app.js') }}"></script>
    </body>
</html>
