@extends('layouts.panel')
@section('title', 'Overview')

@section('content')
<div class="main-top">
    <div>
        <p class="muted mb-0">Welcome back,</p>
        <h1>{{ auth()->user()->displayName() }} 👋</h1>
    </div>
    <a href="{{ route('customer.quotes.create') }}" class="btn btn-accent">@include('partials.icon', ['name' => 'plus']) New request</a>
</div>

@if ($needsAction->isNotEmpty())
    <div class="card alert-card">
        <div class="card-head"><h3>🎉 {{ $needsAction->count() === 1 ? 'A quotation is' : $needsAction->count().' quotations are' }} waiting for your decision</h3></div>
        @foreach ($needsAction as $q)
            <div class="flex between items-center gap-2 wrap" style="padding:.6rem 0;border-top:1px dashed #d8c870">
                <div><b>{{ $q->title }}</b> <span class="muted small">· {{ $q->reference }}</span></div>
                <div class="flex items-center gap-2"><b>{{ money($q->quoted_total) }}</b><a href="{{ route('customer.quotes.show', $q) }}" class="btn btn-sm btn-primary">Review</a></div>
            </div>
        @endforeach
    </div>
@endif

<div class="stat-grid">
    <a class="stat t1" href="{{ route('customer.quotes.index') }}"><small>Total requests</small><b>{{ $stats['total'] }}</b></a>
    <a class="stat t3" href="{{ route('customer.quotes.index', ['status' => 'pending']) }}"><small>Awaiting quotation</small><b>{{ $stats['waiting'] }}</b></a>
    <a class="stat t2" href="{{ route('customer.quotes.index', ['status' => 'quoted']) }}"><small>Quotations ready</small><b>{{ $stats['ready'] }}</b></a>
    <a class="stat t5" href="{{ route('customer.quotes.index') }}"><small>In progress</small><b>{{ $stats['active'] }}</b></a>
    <a class="stat t4" href="{{ route('customer.quotes.index', ['status' => 'completed']) }}"><small>Completed</small><b>{{ $stats['done'] }}</b></a>
</div>

<div class="card flush">
    <div class="card-head"><h3>Recent requests</h3><a class="link small" href="{{ route('customer.quotes.index') }}">View all →</a></div>
    @include('customer.quotes._table', ['quotes' => $recent])
</div>
@endsection
