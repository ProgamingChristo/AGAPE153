<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $successful = Auth::attempt($credentials, $request->boolean('remember'));

        LoginHistory::query()->create([
            'user_id' => $successful ? $request->user()->id : null,
            'email' => $credentials['email'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'successful' => $successful,
        ]);

        if (! $successful) {
            return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->user()->update(['last_login_at' => now()]);

        return redirect()->intended($request->user()->isAdmin() ? route('admin.dashboard') : route('member.dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::query()->create([
            ...$data,
            'role' => 'member',
            'status' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard')->with('status', 'Akun berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
