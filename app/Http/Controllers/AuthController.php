<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'These details do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(Auth::user()->isAdmin() ? route('admin.dashboard') : route('customer.dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'account_type' => ['required', Rule::in(array_keys(User::ACCOUNT_TYPES))],
            'organization' => ['nullable', 'required_unless:account_type,individual', 'string', 'max:150'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'organization.required_unless' => 'Please enter your school / organization name.',
        ]);

        if ($data['account_type'] === 'individual') {
            $data['organization'] = null;
        }

        $user = User::create($data + ['role' => 'customer']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.quotes.create')->with('status', 'Welcome! Your account is ready — tell us what you need printed.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
