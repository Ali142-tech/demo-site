@extends('layouts.site')
@section('title', 'What we print — '.config('business.name'))

@section('content')
<section class="page-hero">
    <span class="halftone"></span>
    <div class="container">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / What we print</div>
        <h1>What we print</h1>
        <p>Six product lines, one friendly shop. Choose a product to see its options.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid grid-3">
            @foreach ($products as $i => $p)
                <a href="{{ route('product', $p) }}" class="pcard reveal">
                    <div class="pcard-art tone-{{ $i % 6 }}">@include('partials.product-art', ['icon' => $p->icon])</div>
                    <div class="pcard-body">
                        <h3>{{ $p->name }}</h3>
                        <p>{{ $p->tagline }}</p>
                        <div class="pcard-foot">
                            <span>
                                <span class="price" style="font-size:1rem">Get a quotation</span>
                            </span>
                            <span class="arrow">@include('partials.icon', ['name' => 'arrow'])</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="panel-card center mt-4 reveal">
            <h3>Need something that is not listed?</h3>
            <p class="muted">Tell us what you have in mind — we can often make custom items too.</p>
            <a href="{{ route('customer.quotes.create') }}" class="btn btn-primary">Describe your requirement @include('partials.icon', ['name' => 'arrow'])</a>
        </div>
    </div>
</section>
@endsection
