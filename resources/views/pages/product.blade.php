@extends('layouts.site')
@section('title', $product->name.' — '.config('business.name'))
@section('description', $product->tagline)

@php($idx = $product->id % 6)
@section('content')
<section class="page-hero">
    <span class="halftone"></span>
    <div class="container">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('services') }}">What we print</a> / {{ $product->name }}</div>
        <h1>{{ $product->name }}</h1>
        <p>{{ $product->tagline }}</p>
    </div>
</section>

<section class="section">
    <div class="container product-layout">
        <div>
            <div class="product-art tone-{{ $idx }}">@include('partials.product-art', ['icon' => $product->icon])</div>

            <h2 style="font-size:1.8rem">About this product</h2>
            <p class="muted" style="font-size:1.08rem">{{ $product->description }}</p>

            @if ($product->features)
                <ul class="tick-list">
                    @foreach ($product->features as $f)<li>{{ $f }}</li>@endforeach
                </ul>
            @endif

            @if ($product->options)
                <h3 class="mt-4">Options available</h3>
                @foreach ($product->options as $label => $choices)
                    <div class="option-group">
                        <div class="label">{{ $label }}</div>
                        <div class="chips">@foreach ($choices as $c)<span class="chip">{{ $c }}</span>@endforeach</div>
                    </div>
                @endforeach
            @endif
        </div>

        <aside class="sticky">
            <div class="panel-card">
                <div class="price" style="font-size:1.6rem">Get a free quotation</div>
                <p class="muted small mt-1">Minimum order: {{ number_format($product->min_quantity) }} {{ \Illuminate\Support\Str::plural($product->price_unit) }}. We price every order individually based on quantity and options, with special rates for bulk and school orders.</p>
                <a href="{{ route('customer.quotes.create', ['product' => $product->slug]) }}" class="btn btn-accent btn-block">Request a quotation @include('partials.icon', ['name' => 'arrow'])</a>
                @guest<p class="small muted center mt-2 mb-0">You will be asked to log in or create a free account.</p>@endguest
                <hr style="border:0;border-top:1px dashed var(--line);margin:1.4rem 0">
                <a class="link small" href="https://wa.me/{{ config('business.whatsapp') }}?text={{ urlencode('Hi, I have a question about '.$product->name) }}" target="_blank" rel="noopener">Questions? Chat on WhatsApp →</a>
            </div>
        </aside>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <h2 class="mb-3" style="font-size:1.8rem">You may also need</h2>
        <div class="grid grid-3">
            @foreach ($others->take(3) as $o)
                <a href="{{ route('product', $o) }}" class="pcard">
                    <div class="pcard-art tone-{{ $o->id % 6 }}">@include('partials.product-art', ['icon' => $o->icon])</div>
                    <div class="pcard-body">
                        <h3>{{ $o->name }}</h3>
                        <p class="mb-0">{{ $o->tagline }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
