@extends('layouts.site')
@section('title', 'Create account — '.config('business.name'))

@section('content')
<div class="auth-wrap">
    <div class="auth-side">
        <span class="halftone t"></span><span class="halftone"></span>
        <div style="position:relative">
            <span class="kicker" style="color:var(--yellow)">Free account</span>
            <h2>Your own space for every print order.</h2>
            <p style="color:#b9bcdc;max-width:26rem">Schools, businesses and individuals each get a private dashboard with their own records.</p>
            <ul class="tick-list">
                <li>Request quotations for any product</li>
                <li>Upload files &amp; student lists securely</li>
                <li>Chat with us on each order</li>
                <li>Printable, itemised quotations</li>
            </ul>
        </div>
    </div>
    <div class="auth-form">
        <div class="box" style="width:min(560px,100%)">
            <h1>Create your account</h1>
            <p class="muted">Already registered? <a class="link" href="{{ route('login') }}">Log in</a></p>
            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf
                <div class="field">
                    <span class="label">I am ordering as</span>
                    <div class="seg">
                        @foreach (['school' => 'school', 'business' => 'box', 'individual' => 'user'] as $type => $ico)
                            <label>
                                <input type="radio" name="account_type" value="{{ $type }}" {{ old('account_type', 'school') === $type ? 'checked' : '' }}>
                                <span>
                                    @if ($type === 'school')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 9 10-5 10 5-10 5z"/><path d="M6 11v5c0 1 2.7 3 6 3s6-2 6-3v-5M22 9v6"/></svg>
                                    @else
                                        @include('partials.icon', ['name' => $ico])
                                    @endif
                                    {{ explode(' /', \App\Models\User::ACCOUNT_TYPES[$type])[0] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="field" id="org-field">
                    <label for="organization">School / college name</label>
                    <input class="input @error('organization') is-invalid @enderror" id="organization" name="organization" value="{{ old('organization') }}" autocomplete="organization">
                    @error('organization')<div class="error">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="name">Your name</label>
                        <input class="input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
                        @error('name')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="phone">Phone / WhatsApp</label>
                        <input class="input @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required inputmode="tel" autocomplete="tel">
                        @error('phone')<div class="error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input class="input @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="field"><label for="city">City</label><input class="input" id="city" name="city" value="{{ old('city') }}" autocomplete="address-level2"></div>
                    <div class="field"><label for="address">Delivery address <span class="muted">(optional)</span></label><input class="input" id="address" name="address" value="{{ old('address') }}" autocomplete="street-address"></div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="password">Password</label>
                        <input class="input @error('password') is-invalid @enderror" type="password" id="password" name="password" required autocomplete="new-password">
                        @error('password')<div class="error">{{ $message }}</div>@enderror
                        <div class="hint">At least 8 characters.</div>
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Confirm password</label>
                        <input class="input" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

                <button class="btn btn-accent btn-block">Create account @include('partials.icon', ['name' => 'arrow'])</button>
            </form>
        </div>
    </div>
</div>
@endsection
