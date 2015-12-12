<!DOCTYPE html>
<html lang="en">
    <head>
        {!! Head::render() !!}
        <link rel="stylesheet" href="{{ elixir('css/app.css') }}">
        <script src="{{ elixir('js/lib.js') }}"></script>
        <script src="{{ elixir('js/app.js') }}"></script>

        @if (Config::get('app.debug'))
            <script type="text/javascript">
                document.write('<script src="//localhost:35729/livereload.js?snipver=1" type="text/javascript"><\/script>')
            </script>
        @endif
    </head>
    <body class="{{ Request::path() == "/" ? "index" : preg_replace('/\//', '-', Request::path()) }}">
        @yield('page-top')
        @yield('page-content')
        @yield('page-bottom')
    </body>
</html>
