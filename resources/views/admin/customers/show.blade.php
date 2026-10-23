@extends('layouts.panel')
@section('title', $customer->displayName())

@section('content')
<div class="main-top">
    <div>
        <a class="link small" href="{{ route('admin.customers.index') }}">← All customers</a>
        <h1 class="mt-1">{{ $customer->displayName() }}</h1>
        <span class="badge plain info">{{ $customer->accountTypeLabel() }}</span>
    </div>
    <a class="btn btn-sm" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/', '', $customer->phone) }}">WhatsApp {{ $customer->name }}</a>
</div>

<div class="cols">
    <div class="card flush">
        <div class="card-head"><h3>Order history ({{ $quotes->count() }})</h3></div>
        @if ($quotes->isEmpty())
            <div class="empty"><p class="mb-0">No requests yet.</p></div>
        @else
            <div class="table-wrap"><table class="table">
                <thead><tr><th>Request</th><th>Items</th><th>Status</th><th class="num">Quoted</th><th></th></tr></thead>
                <tbody>
                @foreach ($quotes as $q)
                    <tr>
                        <td><a class="row-link" href="{{ route('admin.quotes.show', $q) }}">{{ $q->title }}</a><br><span class="muted small">{{ $q->reference }} · {{ $q->created_at->format('d M Y') }}</span></td>
                        <td>{{ $q->items_count }}</td>
                        <td><span class="badge {{ $q->statusTone() }}">{{ $q->statusLabel() }}</span></td>
                        <td class="num">{{ $q->hasQuote() ? money($q->quoted_total) : '—' }}</td>
                        <td class="right"><a class="btn btn-sm" href="{{ route('admin.quotes.show', $q) }}">Open</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        @endif
    </div>

    <div class="card">
        <h3>Contact details</h3>
        <dl class="dl">
            <dt>Contact</dt><dd>{{ $customer->name }}</dd>
            <dt>Phone</dt><dd>{{ $customer->phone }}</dd>
            <dt>Email</dt><dd>{{ $customer->email }}</dd>
            <dt>City</dt><dd>{{ $customer->city ?: '—' }}</dd>
            <dt>Address</dt><dd>{{ $customer->address ?: '—' }}</dd>
            <dt>Joined</dt><dd>{{ $customer->created_at->format('d M Y') }}</dd>
        </dl>
    </div>
</div>
@endsection
