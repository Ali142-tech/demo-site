@extends('layouts.panel')
@section('title', 'Profile & settings')

@section('content')
<div class="main-top"><h1>Profile &amp; settings</h1></div>

<div class="cols">
    <div class="card">
        <h3>Account details</h3>
        <form method="POST" action="{{ route('customer.profile.update') }}" novalidate>
            @csrf @method('PUT')
            <div class="field">
                <span class="label">Account type</span>
                <div class="seg">
                    @foreach (\App\Models\User::ACCOUNT_TYPES as $type => $label)
                        <label><input type="radio" name="account_type" value="{{ $type }}" {{ old('account_type', $user->account_type) === $type ? 'checked' : '' }}><span>{{ explode(' /', $label)[0] }}</span></label>
                    @endforeach
                </div>
            </div>
            <div class="field" id="org-field">
                <label for="organization">School / organization name</label>
                <input class="input @error('organization') is-invalid @enderror" id="organization" name="organization" value="{{ old('organization', $user->organization) }}">
                @error('organization')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <div class="field"><label for="name">Contact person</label><input class="input" id="name" name="name" value="{{ old('name', $user->name) }}" required></div>
                <div class="field"><label for="phone">Phone / WhatsApp</label><input class="input" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required></div>
            </div>
            <div class="field"><label for="email">Email</label><input class="input" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required></div>
            <div class="form-row">
                <div class="field"><label for="city">City</label><input class="input" id="city" name="city" value="{{ old('city', $user->city) }}"></div>
                <div class="field"><label for="address">Delivery address</label><input class="input" id="address" name="address" value="{{ old('address', $user->address) }}"></div>
            </div>
            <button class="btn btn-primary">Save changes</button>
        </form>
    </div>

    <div class="card">
        <h3>Change password</h3>
        <form method="POST" action="{{ route('customer.profile.password') }}">
            @csrf @method('PUT')
            <div class="field"><label for="current_password">Current password</label><input class="input" type="password" id="current_password" name="current_password" required autocomplete="current-password"></div>
            <div class="field"><label for="password">New password</label><input class="input" type="password" id="password" name="password" required autocomplete="new-password"></div>
            <div class="field"><label for="password_confirmation">Confirm new password</label><input class="input" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"></div>
            <button class="btn">Update password</button>
        </form>
    </div>
</div>
@endsection
