@extends('tracking.pdf.layout')

@section('content')
<table width="100%" cellspacing="0" cellpadding="3">
    <thead>
        <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
            <th align="center">Date</th>
            <th align="center">Start</th>
            <th align="center">End</th>
            <th align="center">Co-worker</th>
            <th align="center">Customer</th>
            <th align="center">Project</th>
            <th align="center">Service</th>
            <th align="center">Description</th>
            <th align="center">Billable</th>
            <th align="center">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $item)
        <tr style="border-bottom: 1px solid #a1a3a4;">
            <td>{{ \Carbon\Carbon::parse($item->date_from)->format('D d M Y') }}</td>
            <td align="center">{{ \Carbon\Carbon::parse($item->date_from)->format('H:i') }}</td>
            <td align="center">{{ \Carbon\Carbon::parse($item->date_from)->format('H:i') }}</td>
            <td>@if ($item->user){{ $item->user->full_name }}@endif</td>
            <td>@if ($item->project && $item->project->client){{ $item->project->client->name }}@endif</td>
            <td>@if ($item->project){{ $item->project->name }}@endif</td>
            <td>@if ($item->service){{ $item->service->name }}@endif</td>
            <td>{{ $item->description }}</td>
            <td align="center">@if ($item->billable) Yes @else No @endif</td>
            <td>{{ $item->amount }}</td>
        </tr>
    @empty
        <tr>
            <td align="center" colspan="10">No data</td>
        </tr>
    @endforelse
    </tbody>
</table>
@endsection
