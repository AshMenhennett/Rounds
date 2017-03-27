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
    <body class="with-colour">
        <div id="app">
            @include('includes.partials._upgrade_browser_danger')
            <div class="jumbotron">
                <div class="container-fluid">
                    <h1>Ecosystem</h1>
                    @if (count($buttons))
                        <p>The latest links!</p>
                        @foreach ($buttons as $button)
                            <p class="text-center"><a class="btn btn-success btn-lg" href="{{ $button->destination() }}">{{ $button->value }} &nbsp; <span class="glyphicon glyphicon-{{ $button->hasFile() ? 'file' : 'new-window' }}"></span></a></p>
                        @endforeach
                    @else
                        <p>No links yet..</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- Scripts -->
        <script src="{{ elixir('js/app.js') }}"></script>
    </body>
</html>