<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — {{ config('business.name') }}</title>
    <meta name="robots" content="noindex">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='11' fill='%2312142b'/%3E%3Ccircle cx='14' cy='14' r='6' fill='%2300aeef'/%3E%3Ccircle cx='27' cy='14' r='6' fill='%23ec008c'/%3E%3Ccircle cx='14' cy='27' r='6' fill='%23ffd400'/%3E%3Ccircle cx='27' cy='27' r='6' fill='%23fff'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $unreadMessages = $isAdmin ? \App\Models\ContactMessage::whereNull('read_at')->count() : 0;
    $pendingQuotes = $isAdmin ? \App\Models\QuoteRequest::where('status', 'pending')->count() : 0;
    $needsAction = ! $isAdmin ? $user->quoteRequests()->where('status', 'quoted')->count() : 0;
@endphp

<div class="mobile-bar">
    <a href="{{ route('home') }}" class="brand">@include('partials.brand-mark')<span>{{ config('business.name') }}</span></a>
    <button class="icon-btn" data-toggle-sidebar aria-label="Open menu">@include('partials.icon', ['name' => 'menu'])</button>
</div>
<div class="scrim" id="scrim"></div>

<div class="panel">
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('home') }}" class="brand">@include('partials.brand-mark')<span>{{ config('business.name') }}</span></a>

        @if ($isAdmin)
            <div class="side-label">Shop</div>
            <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">@include('partials.icon', ['name' => 'home']) Overview</a>
            <a class="side-link {{ request()->routeIs('admin.quotes.*') ? 'active' : '' }}" href="{{ route('admin.quotes.index') }}">@include('partials.icon', ['name' => 'quote']) Quote requests @if ($pendingQuotes)<span class="count">{{ $pendingQuotes }}</span>@endif</a>
            <a class="side-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">@include('partials.icon', ['name' => 'users']) Customers</a>
            <a class="side-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">@include('partials.icon', ['name' => 'box']) Products</a>
            <a class="side-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}" href="{{ route('admin.messages.index') }}">@include('partials.icon', ['name' => 'mail']) Enquiries @if ($unreadMessages)<span class="count">{{ $unreadMessages }}</span>@endif</a>
            <div class="side-label">Site</div>
            <a class="side-link" href="{{ route('home') }}" target="_blank">@include('partials.icon', ['name' => 'eye']) View website</a>
        @else
            <div class="side-label">My account</div>
            <a class="side-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">@include('partials.icon', ['name' => 'home']) Overview</a>
            <a class="side-link {{ request()->routeIs('customer.quotes.create') ? 'active' : '' }}" href="{{ route('customer.quotes.create') }}">@include('partials.icon', ['name' => 'plus']) New request</a>
            <a class="side-link {{ request()->routeIs('customer.quotes.index', 'customer.quotes.show') ? 'active' : '' }}" href="{{ route('customer.quotes.index') }}">@include('partials.icon', ['name' => 'quote']) My orders & quotes @if ($needsAction)<span class="count">{{ $needsAction }}</span>@endif</a>
            <a class="side-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}" href="{{ route('customer.profile') }}">@include('partials.icon', ['name' => 'settings']) Profile & settings</a>
            <div class="side-label">Help</div>
            <a class="side-link" href="https://wa.me/{{ config('business.whatsapp') }}" target="_blank" rel="noopener">@include('partials.icon', ['name' => 'chat']) WhatsApp us</a>
            <a class="side-link" href="{{ route('home') }}">@include('partials.icon', ['name' => 'eye']) Back to website</a>
        @endif

        <div class="side-user">
            <span class="avatar">{{ $user->initials() }}</span>
            <div><b>{{ $user->displayName() }}</b><small>{{ $isAdmin ? 'Administrator' : $user->accountTypeLabel() }}</small></div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="icon-btn" title="Log out" aria-label="Log out">@include('partials.icon', ['name' => 'logout'])</button>
            </form>
        </div>
    </aside>

    <main class="main" id="main">
        @include('partials.flash')
        @yield('content')
    </main>
</div>

<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
</body>
</html>
