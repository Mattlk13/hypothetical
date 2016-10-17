<!DOCTYPE html>
<html lang="en">
    @set('page_title', (isset($title) ? $title . ' - ' : '') . env('SITE_NAME'))
    @set('device_mobile', preg_match('/Mobi/', Request::header('User-Agent')) || preg_match('/iP(hone|ad|od);/', Request::header('User-Agent')))

    <head>
        <title>{{ $page_title }}</title>

        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta name="title" content="{{ $page_title }}" />
        <meta name="description" content="{{ env('SITE_DESC') }}" />
        <meta name="dc:title" content="{{ $page_title }}" />
        <meta name="dc:description" content="{{ env('SITE_DESC') }}" />

        <meta property="og:type" content="article" />
        <meta property="og:title" content="{{ $page_title }}" />
        <meta property="og:description" content="{{ env('SITE_DESC') }}" />
        <meta property="og:url" content="{{ Request::url() }}" />
        <meta property="og:image" content="{{ asset('/img/logo.png') }}" />

        <meta name="twitter:card" content="summary" />
        <meta name="twitter:title" content="{{ $page_title }}" />
        <meta name="twitter:description" content="{{ env('SITE_DESC') }}" />
        <meta name="twitter:image" content="{{ asset('/img/logo.png') }}" />

        <link rel="shortcut icon" href="{{ URL::to('/') }}/favicon.ico" />
        <link rel="icon" href="{{ URL::to('/') }}/favicon.ico" type="image/x-icon" />
        <link rel="icon" href="{{ URL::to('/') }}/favicon.png" type="image/png" />
        <link rel="canonical" href="{{ Request::url() }}" />

        @yield('page-includes')

        @if(Config::get('app.debug'))
            <script type="text/javascript">
                document.write('<script src="//{{ env('LR_HOST', 'localhost') }}:35729/livereload.js?snipver=1" type="text/javascript"><\/script>')
            </script>
        @endif
    </head>

    <body class="page-{{ Request::path() == '/' ? 'index' : preg_replace('/\/.*/', '', Request::path()) }} {{ $device_mobile ? 'mobile-browser' : 'desktop-browser' }}">
        @yield('page-top')

        <div id="page-content">
            @yield('content')
        </div>

        @yield('page-bottom')
    </body>
</html>
