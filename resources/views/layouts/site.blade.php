<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('business.name').' — '.config('business.tagline'))</title>
    <meta name="description" content="@yield('description', 'Custom printing for schools and businesses: photocopies, test sheets, ties, badges, printed scotch tape and PVC ID cards. Create an account and get a quotation fast.')">
    <meta name="theme-color" content="#12142b">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='11' fill='%2312142b'/%3E%3Ccircle cx='14' cy='14' r='6' fill='%2300aeef'/%3E%3Ccircle cx='27' cy='14' r='6' fill='%23ec008c'/%3E%3Ccircle cx='14' cy='27' r='6' fill='%23ffd400'/%3E%3Ccircle cx='27' cy='27' r='6' fill='%23fff'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
<a class="skip" href="#main">Skip to content</a>

<div class="topbar">
    <div class="container">
        <span class="hide-sm">{{ config('business.hours') }}</span>
        <span>
            <a href="tel:{{ preg_replace('/[^+\d]/', '', config('business.phone')) }}">{{ config('business.phone') }}</a>@if(config('business.phone_2')) / <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('business.phone_2')) }}">{{ config('business.phone_2') }}</a>@endif
            &nbsp;·&nbsp;
            <a href="mailto:{{ config('business.email') }}">{{ config('business.email') }}</a>
        </span>
    </div>
</div>

<header class="nav">
    <div class="container">
        <a href="{{ route('home') }}" class="brand">@include('partials.brand-mark')<span>{{ config('business.name') }}</span></a>

        <button class="burger" id="burger" aria-label="Menu" aria-controls="nav-links" aria-expanded="false"><span></span></button>

        <nav class="nav-links" id="nav-links">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('services') }}" class="{{ request()->routeIs('services', 'product') ? 'active' : '' }}">What we print</a>
            <a href="{{ route('how') }}" class="{{ request()->routeIs('how') ? 'active' : '' }}">How it works</a>
            <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
            <div class="nav-cta">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('customer.dashboard') }}" class="btn btn-sm">My dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-ghost">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Get a quotation @include('partials.icon', ['name' => 'arrow'])</a>
                @endauth
            </div>
        </nav>
    </div>
</header>

<main id="main">
    @if (session('status') || $errors->any())
        <div class="container mt-3">
            @include('partials.flash')
        </div>
    @endif
    @yield('content')
</main>

<footer class="footer">
    <div class="reg"><i></i><i></i><i></i><i></i></div>
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="{{ route('home') }}" class="brand">@include('partials.brand-mark')<span>{{ config('business.name') }}</span></a>
                <p>{{ config('business.tagline') }}. We print for schools, businesses and individuals — from a single ID card to thousands of exam sheets.</p>
            </div>
            <div>
                <h4>What we print</h4>
                <ul>
                    @foreach (\App\Models\Product::active()->get() as $p)
                        <li><a href="{{ route('product', $p) }}">{{ $p->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4>Company</h4>
                <ul>
                    <li><a href="{{ route('how') }}">How it works</a></li>
                    <li><a href="{{ route('contact') }}">Contact us</a></li>
                    <li><a href="{{ route('register') }}">Create account</a></li>
                    <li><a href="{{ route('login') }}">Customer login</a></li>
                </ul>
            </div>
            <div>
                <h4>Visit or call</h4>
                <ul>
                    <li>{{ config('business.address') }}</li>
                    <li>{{ config('business.phone') }}@if(config('business.phone_2')) · {{ config('business.phone_2') }}@endif</li>
                    <li>{{ config('business.email') }}</li>
                    <li>{{ config('business.hours') }}</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} {{ config('business.name') }}. All rights reserved.</span>
            <span>Made with care, printed with pride.</span>
        </div>
    </div>
</footer>

<a class="wa-float" href="https://wa.me/{{ config('business.whatsapp') }}?text={{ urlencode('Hello '.config('business.name').', I would like a quotation.') }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    @include('partials.icon', ['name' => 'whatsapp'])
</a>

<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
</body>
</html>
