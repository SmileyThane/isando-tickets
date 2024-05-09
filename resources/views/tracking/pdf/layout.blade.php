<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
</head>
<body>
<header class="header" style="border-left: 1px solid #000;">
        <span class="header__name">
            {{ $reportName }}
        </span>
    <br>
    <span class="header__user">
            <b>{{ $user->full_name }}</b>
        </span>
    <br>
    <span class="header__date">
            {{ $period }}
        </span>
</header>

<footer class="footer text-right">
    <div class="footer__page-number text-right" style="float: right">
        Footer
    </div>
</footer>
<main>
    @yield('content')
</main>
</body>
</html>
