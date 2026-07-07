<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MemberController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('member.dashboard', [
            'orders' => $request->user()->orders()->with('items')->latest()->take(8)->get(),
            'wishlists' => $request->user()->wishlists()->with('product.category')->latest()->take(8)->get(),
        ]);
    }

    public function profile(Request $request)
    {
        return view('member.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:160'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Profil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', 'Password diperbarui.');
    }
}
