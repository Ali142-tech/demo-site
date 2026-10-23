@extends('layouts.panel')
@section('title', 'Customers')

@section('content')
<div class="main-top"><h1>Customers</h1></div>

<div class="tabs">
    <a class="tab {{ ! $type ? 'active' : '' }}" href="{{ route('admin.customers.index', ['q' => $search]) }}">All</a>
    @foreach (\App\Models\User::ACCOUNT_TYPES as $key => $label)
        <a class="tab {{ $type === $key ? 'active' : '' }}" href="{{ route('admin.customers.index', ['type' => $key, 'q' => $search]) }}">{{ $label }}</a>
    @endforeach
</div>

<form class="searchbar" method="GET">
    @if ($type)<input type="hidden" name="type" value="{{ $type }}">@endif
    <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Search name, school, email, phone…">
    <button class="btn btn-sm">@include('partials.icon', ['name' => 'search']) Search</button>
</form>

<div class="card flush">
    @if ($customers->isEmpty())
        <div class="empty"><h3>No customers found</h3></div>
    @else
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Customer</th><th>Type</th><th>Contact</th><th>Orders</th><th class="num">Accepted value</th><th>Joined</th><th></th></tr></thead>
            <tbody>
            @foreach ($customers as $c)
                <tr>
                    <td><a class="row-link" href="{{ route('admin.customers.show', $c) }}">{{ $c->displayName() }}</a>@if ($c->organization)<br><span class="muted small">{{ $c->name }}</span>@endif</td>
                    <td><span class="badge plain info">{{ explode(' /', $c->accountTypeLabel())[0] }}</span></td>
                    <td>{{ $c->phone }}<br><span class="muted small">{{ $c->email }}</span></td>
                    <td>{{ $c->quote_requests_count }}</td>
                    <td class="num">{{ money($c->spent ?? 0) }}</td>
                    <td>{{ $c->created_at->format('d M Y') }}</td>
                    <td class="right"><a class="btn btn-sm" href="{{ route('admin.customers.show', $c) }}">Records</a></td>
                </tr>
            @endforeach
            </tbody>
        </table></div>
    @endif
</div>
@include('partials.pager', ['paginator' => $customers])
@endsection
