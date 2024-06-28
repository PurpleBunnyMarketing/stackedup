<!DOCTYPE html>
<html>

<head>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <title>{{ env('APP_NAME') }}</title>
    <!-- Font family Included -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&display=swap" rel="stylesheet">
    {{-- @include('frontend.layouts.includes.css') --}}
    <style type="text/css">
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p {
            margin: 0;
        }

        table {
            border: 0px;
        }
    </style>
</head>

<body style="margin: 0; padding: 0;">
    <table style="font-family: 'Open Sans', sans-serif;" cellpadding="0" cellspacing="0" bgcolor="#012549" width="100%">
        <tr>
            <td>
                <table width="100%" border-collapse="collapse" border-spacing="0" cellpadding="0" cellspacing="0"
                    style="color: #232323;  min-width: 100%; " bgcolor="#fff" align="center">
                    <tr>
                        <td>
                            <table border-collapse="collapse" border-spacing="0" width="100%" cellpadding="0"
                                cellspacing="0" bgcolor="#012549"
                                style="min-width: 100%; font-family: 'Open Sans', sans-serif;">
                                @include('emails.layouts.header')
                                @stack('banner-section')
                            </table>
                        </td>
                    </tr>
                    @yield('main-content')
                    @include('emails.layouts.footer')
                </table>
            </td>
        </tr>
    </table>
</body>

</html>