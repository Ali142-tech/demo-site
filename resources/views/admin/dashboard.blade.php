@extends('layouts.panel')
@section('title', 'Admin overview')

@section('content')
<div class="main-top">
    <div><p class="muted mb-0">{{ now()->format('l, d F Y') }}</p><h1>Shop overview</h1></div>
    <a href="{{ route('admin.quotes.index', ['status' => 'pending']) }}" class="btn btn-accent">Quote the waiting requests</a>
</div>

<div class="stat-grid">
    <a class="stat t3" href="{{ route('admin.quotes.index', ['status' => 'pending']) }}"><small>Needs a quotation</small><b>{{ $stats['pending'] }}</b></a>
    <a class="stat t2" href="{{ route('admin.quotes.index', ['status' => 'quoted']) }}"><small>Quoted, awaiting reply</small><b>{{ $stats['quoted'] }}</b></a>
    <a class="stat t5" href="{{ route('admin.quotes.index') }}"><small>Jobs in progress</small><b>{{ $stats['active'] }}</b></a>
    <a class="stat t1" href="{{ route('admin.customers.index') }}"><small>Customers</small><b>{{ $stats['customers'] }}</b></a>
    <a class="stat t4" href="{{ route('admin.messages.index') }}"><small>Unread enquiries</small><b>{{ $stats['messages'] }}</b></a>
    <div class="stat t1"><small>Accepted order value</small><b style="font-size:1.5rem">{{ money($stats['revenue']) }}</b></div>
</div>

<div class="cols">
    <div class="card flush">
        <div class="card-head"><h3>Waiting for your quotation</h3></div>
        @if ($pendingQuotes->isEmpty())
            <div class="empty"><h3>All caught up 🎉</h3><p class="mb-0">No requests are waiting for a quotation.</p></div>
        @else
            <div class="table-wrap"><table class="table">
                <thead><tr><th>Customer</th><th>Request</th><th>Items</th><th>Needed by</th><th></th></tr></thead>
                <tbody>
                @foreach ($pendingQuotes as $q)
                    <tr>
                        <td><b>{{ $q->user->displayName() }}</b><br><span class="muted small">{{ $q->created_at->diffForHumans() }}</span></td>
                        <td>{{ $q->title }}<br><span class="muted small">{{ $q->reference }}</span></td>
                        <td>{{ $q->items_count }}</td>
                        <td>{{ $q->needed_by?->format('d M') ?? '—' }}</td>
                        <td class="right"><a class="btn btn-sm btn-primary" href="{{ route('admin.quotes.show', $q) }}">Quote</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        @endif
    </div>

    <div class="card">
        <h3>Latest activity</h3>
        @foreach ($recent as $q)
            <a href="{{ route('admin.quotes.show', $q) }}" class="flex between items-center gap-2" style="padding:.6rem 0;border-top:1px dashed var(--line)">
                <span style="min-width:0"><b style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $q->user->displayName() }}</b><span class="muted small">{{ $q->reference }} · {{ $q->updated_at->diffForHumans() }}</span></span>
                <span class="badge {{ $q->statusTone() }}">{{ $q->statusLabel() }}</span>
            </a>
        @endforeach
    </div>
</div>
@endsection
