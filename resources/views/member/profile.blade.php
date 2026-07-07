@extends('layouts.app')

@section('title', 'Profile - Agape153')

@section('content')
    <section class="bg-white py-12">
        <div class="agape-container grid gap-8 lg:grid-cols-2">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Profile</p>
                <h1 class="mt-3 text-4xl font-black text-slate-950">Account settings.</h1>
                <form class="mt-8 grid gap-4 rounded-2xl border border-slate-200 bg-[#f8faf9] p-6" method="POST" action="{{ route('member.profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <input class="rounded-xl border border-slate-200 px-4 py-3" name="name" value="{{ old('name', $user->name) }}" required>
                    <input class="rounded-xl border border-slate-200 px-4 py-3 bg-slate-100" value="{{ $user->email }}" disabled>
                    <input class="rounded-xl border border-slate-200 px-4 py-3" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="WhatsApp number">
                    <input class="rounded-xl border border-slate-200 px-4 py-3" name="company_name" value="{{ old('company_name', $user->company_name) }}" placeholder="Company name">
                    <button class="btn-primary" type="submit">Save Profile</button>
                </form>
            </div>
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Security</p>
                <h2 class="mt-3 text-4xl font-black text-slate-950">Change password.</h2>
                <form class="mt-8 grid gap-4 rounded-2xl border border-slate-200 bg-[#f8faf9] p-6" method="POST" action="{{ route('member.password.update') }}">
                    @csrf
                    @method('PATCH')
                    <input class="rounded-xl border border-slate-200 px-4 py-3" type="password" name="current_password" placeholder="Current password" required>
                    <input class="rounded-xl border border-slate-200 px-4 py-3" type="password" name="password" placeholder="New password" required>
                    <input class="rounded-xl border border-slate-200 px-4 py-3" type="password" name="password_confirmation" placeholder="Confirm new password" required>
                    <button class="btn-secondary" type="submit">Update Password</button>
                </form>
            </div>
        </div>
    </section>
@endsection
