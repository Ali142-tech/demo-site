@extends('layouts.site')
@section('title', 'Log in — '.config('business.name'))

@section('content')
<div class="auth-wrap">
    <div class="auth-side">
        <span class="halftone t"></span><span class="halftone"></span>
        <div style="position:relative">
            <span class="kicker" style="color:var(--yellow)">Customer portal</span>
            <h2>Welcome back.</h2>
            <p style="color:#b9bcdc;max-width:26rem">Log in to track your orders, review quotations and send new requirements.</p>
            <ul class="tick-list">
                <li>See every quotation and its status</li>
                <li>Accept quotes with a single click</li>
                <li>Reorder past jobs in seconds</li>
            </ul>
        </div>
    </div>
    <div class="auth-form">
        <div class="box">
            <h1>Log in</h1>
            <p class="muted">New here? <a class="link" href="{{ route('register') }}">Create a free account</a></p>
            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <input class="input @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input class="input" type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <label class="check mb-2"><input type="checkbox" name="remember" value="1"> Keep me logged in</label>
                <button class="btn btn-primary btn-block">Log in @include('partials.icon', ['name' => 'arrow'])</button>
            </form>
            <p class="small muted mt-3">Forgot your password? Message us on <a class="link" href="https://wa.me/{{ config('business.whatsapp') }}" target="_blank" rel="noopener">WhatsApp</a> and we will reset it.</p>
        </div>
    </div>
</div>
@endsection
