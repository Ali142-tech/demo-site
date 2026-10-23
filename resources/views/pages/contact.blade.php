@extends('layouts.site')
@section('title', 'Contact — '.config('business.name'))

@section('content')
<section class="page-hero">
    <span class="halftone"></span>
    <div class="container">
        <div class="crumbs"><a href="{{ route('home') }}">Home</a> / Contact</div>
        <h1>Let’s talk print</h1>
        <p>Questions before you order? Send us a message, call, or pop into the shop.</p>
    </div>
</section>

<section class="section">
    <div class="container product-layout contact-layout">
        <div class="panel-card">
            <h2 style="font-size:1.8rem">Send us a message</h2>
            <form method="POST" action="{{ route('contact.send') }}" novalidate>
                @csrf
                <div class="honey" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                <div class="form-row">
                    <div class="field"><label for="name">Your name</label><input class="input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>@error('name')<div class="error">{{ $message }}</div>@enderror</div>
                    <div class="field"><label for="phone">Phone</label><input class="input" id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel">@error('phone')<div class="error">{{ $message }}</div>@enderror</div>
                </div>
                <div class="field"><label for="email">Email</label><input class="input @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}">@error('email')<div class="error">{{ $message }}</div>@enderror</div>
                <div class="field"><label for="subject">Subject</label><input class="input" id="subject" name="subject" value="{{ old('subject') }}"></div>
                <div class="field"><label for="message">Message</label><textarea class="input @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>@error('message')<div class="error">{{ $message }}</div>@enderror</div>
                <button class="btn btn-primary">Send message @include('partials.icon', ['name' => 'arrow'])</button>
            </form>
        </div>

        <div>
            <div class="panel-card" style="box-shadow:6px 6px 0 var(--cyan)">
                <h3>Shop details</h3>
                <dl class="dl" style="grid-template-columns:34px 1fr;align-items:start">
                    <dt>@include('partials.icon', ['name' => 'map'])</dt><dd>{{ config('business.address') }}</dd>
                    <dt>@include('partials.icon', ['name' => 'phone'])</dt><dd><a href="tel:{{ preg_replace('/[^+\d]/', '', config('business.phone')) }}">{{ config('business.phone') }}</a>@if(config('business.phone_2')) · <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('business.phone_2')) }}">{{ config('business.phone_2') }}</a>@endif</dd>
                    <dt>@include('partials.icon', ['name' => 'mail'])</dt><dd><a href="mailto:{{ config('business.email') }}">{{ config('business.email') }}</a></dd>
                    <dt>@include('partials.icon', ['name' => 'clock'])</dt><dd>{{ config('business.hours') }}</dd>
                </dl>
                <a class="btn btn-block mt-2" style="background:#25d366" href="https://wa.me/{{ config('business.whatsapp') }}" target="_blank" rel="noopener">Chat on WhatsApp</a>
            </div>
        </div>
    </div>
</section>
@endsection
