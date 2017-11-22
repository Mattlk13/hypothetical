<!DOCTYPE html>
<html>
    <head>
        <title>@yield('error-title') - {{ env('APP_NAME') }}</title>

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family: Impact, Charcoal, sans-serif;
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
                margin-bottom: 40px;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="content">
                <div class="title">@yield('error-title')</div>
            </div>
        </div>
    </body>
</html>