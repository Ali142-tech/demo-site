@extends('layouts.site')
@section('title', 'How it works — '.config('business.name'))

@section('content')
<section class="page-hero">
    <span class="halftone"></span>
    <div class="container">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / How it works</div>
        <h1>How ordering works</h1>
        <p>A simple, transparent process — you always know where your order stands.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:900px">
        @foreach ([
            ['Create your account', 'Sign up as a <b>school</b>, <b>business</b> or <b>individual</b>. Your organization details are saved so you never type them again, and every order you place stays in your private dashboard.'],
            ['Send your requirement', 'Add one or more items — choose the product, quantity and options (size, colour, finish…). Attach your designs, logos, student lists or photos and tell us when you need it by.'],
            ['Get your quotation', 'We review your request and send an <b>itemised quotation</b> with unit prices, delivery and validity date. You are notified by email, and you can print the quotation or share it with your management.'],
            ['Discuss &amp; accept', 'Need a change? Message us on the order page and we will revise the quote. When you are happy, click <b>Accept</b> — that is your go-ahead for us to start.'],
            ['Production &amp; delivery', 'Follow your order through <i>In production</i> and <i>Ready for delivery</i>. We deliver to your door, or you can collect from our shop.'],
            ['Reorder anytime', 'Next term or next year? Open an earlier order and press <b>Order again</b> — we copy everything so you only update what changed.'],
        ] as $i => [$title, $text])
            <div class="flex gap-2 reveal" style="margin-bottom:2rem;align-items:flex-start">
                <div style="flex:none;width:56px;height:56px;border-radius:50%;background:var(--yellow);border:2px solid var(--ink);display:grid;place-items:center;font:800 1.5rem var(--font-head);box-shadow:3px 3px 0 var(--ink)">{{ $i + 1 }}</div>
                <div>
                    <h3 style="font-size:1.5rem;margin-bottom:.3rem">{!! $title !!}</h3>
                    <p class="muted mb-0" style="font-size:1.05rem">{!! $text !!}</p>
                </div>
            </div>
        @endforeach

        <div class="center mt-4">
            <a href="{{ route('register') }}" class="btn btn-accent">Start your first request @include('partials.icon', ['name' => 'arrow'])</a>
        </div>
    </div>
</section>
@endsection
