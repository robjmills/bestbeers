<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Bestbeers.in</title>
        <meta name="viewport" content="width=device-width">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="//fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
        {{ HTML::style('css/main.css') }}
        @yield('styles')
    </head>

    <body>
    <div class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">BESTBEERS<small>.in</small></a>
            </div>

        </div>
    </div>
    <div class="container">
        @yield('content')
    </div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    {{ HTML::script('vendor/jsviews/jsviews.min.js') }}
    {{ HTML::script('scripts/main.js') }}
    @yield('scripts')
    </body>
</html>