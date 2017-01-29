<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="{{ url('/favicon.png') }}" rel="icon" type="image/png">

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
    </head>
    <body>
        <div id="app">
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
