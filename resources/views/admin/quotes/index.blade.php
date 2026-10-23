@extends('layouts.panel')
@section('title', 'Quote requests')

@section('content')
<div class="main-top"><h1>Quote requests</h1></div>

<div class="tabs">
    <a class="tab {{ ! $status ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['q' => $search]) }}">All<em>{{ $counts->sum() }}</em></a>
    @foreach (\App\Models\QuoteRequest::STATUSES as $key => $s)
        <a class="tab {{ $status === $key ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['status' => $key, 'q' => $search]) }}">{{ $s['label'] }}<em>{{ $counts[$key] ?? 0 }}</em></a>
    @endforeach
</div>

<form class="searchbar" method="GET">
    @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
    <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Search school, name, reference…">
    <button class="btn btn-sm">@include('partials.icon', ['name' => 'search']) Search</button>
</form>

<div class="card flush">
    @if ($quotes->isEmpty())
        <div class="empty"><h3>Nothing here</h3><p class="mb-0">No requests match this filter.</p></div>
    @else
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Request</th><th>Customer</th><th>Items</th><th>Needed by</th><th>Status</th><th class="num">Quoted</th><th></th></tr></thead>
            <tbody>
            @foreach ($quotes as $q)
                <tr>
                    <td><a class="row-link" href="{{ route('admin.quotes.show', $q) }}">{{ $q->title }}</a><br><span class="muted small">{{ $q->reference }} · {{ $q->created_at->format('d M Y') }}</span></td>
                    <td>{{ $q->user->displayName() }}<br><span class="muted small">{{ $q->user->accountTypeLabel() }}</span></td>
                    <td>{{ $q->items_count }}</td>
                    <td>{{ $q->needed_by?->format('d M Y') ?? '—' }}</td>
                    <td><span class="badge {{ $q->statusTone() }}">{{ $q->statusLabel() }}</span></td>
                    <td class="num">{{ $q->hasQuote() ? money($q->quoted_total) : '—' }}</td>
                    <td class="right"><a class="btn btn-sm" href="{{ route('admin.quotes.show', $q) }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table></div>
    @endif
</div>
@include('partials.pager', ['paginator' => $quotes])
@endsection
