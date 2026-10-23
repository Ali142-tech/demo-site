@extends('layouts.panel')
@section('title', 'My orders & quotes')

@section('content')
<div class="main-top">
    <h1>My orders &amp; quotes</h1>
    <a href="{{ route('customer.quotes.create') }}" class="btn btn-accent">@include('partials.icon', ['name' => 'plus']) New request</a>
</div>

<div class="tabs">
    <a class="tab {{ ! $status ? 'active' : '' }}" href="{{ route('customer.quotes.index') }}">All</a>
    @foreach (\App\Models\QuoteRequest::STATUSES as $key => $s)
        <a class="tab {{ $status === $key ? 'active' : '' }}" href="{{ route('customer.quotes.index', ['status' => $key]) }}">{{ $s['label'] }}</a>
    @endforeach
</div>

<div class="card flush">
    @include('customer.quotes._table', ['quotes' => $quotes])
</div>

@if ($quotes->hasPages())
    @include('partials.pager', ['paginator' => $quotes])
@endif
@endsection
