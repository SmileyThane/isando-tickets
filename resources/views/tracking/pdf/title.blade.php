<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    {{ $styles }}
</head>
<body>
<div class="page-1 title">
    <div class="page-1__title text-center">
        {{ $reportName }}
    </div>
    <div class="page-1__user text-center">
        {{ $user->full_name }}
    </div>
    <div class="page-1__date text-center">
        {{ $period }}
    </div>
    <br>
    <br>
    <div class="page-1__total-time text-center">
            <span class="total-time__title text-center">
                Total time
                <hr width="150">
            </span>
        <span class="total-time__time text-center">{{ $totalTime }}</span>
    </div>
    <div class="page-1__created-at text-center">
        Created at {{ \Carbon\Carbon::now()->format('d F Y H:i') }} o'clock
    </div>
</div>
</body>
</html>
