@extends('layouts.app')

@section('title', 'Profile - Agape153')

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30" src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1800&q=80" alt="Account settings">
            <div class="absolute inset-0 bg-slate-950/72"></div>
        </div>
        <div class="agape-container relative py-12" data-reveal>
            <p class="section-kicker text-amber-200"><x-icon name="user" class="h-4 w-4" />Profile</p>
            <h1 class="mt-3 max-w-4xl text-4xl font-black leading-tight sm:text-6xl">Account settings.</h1>
            <p class="mt-4 max-w-2xl leading-8 text-slate-200">Keep contact details current for order confirmation, invoice, and WhatsApp follow-up.</p>
        </div>
    </section>

    <section class="section-pad bg-[#f8faf9]">
        <div class="agape-container grid gap-8 lg:grid-cols-2">
            <form class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8" method="POST" action="{{ route('member.profile.update') }}" data-reveal>
                @csrf
                @method('PATCH')
                <p class="section-kicker"><x-icon name="user" class="h-4 w-4" />Profile Data</p>
                <h2 class="mt-2 text-2xl font-black text-slate-950">Buyer information</h2>
                <div class="mt-6 grid gap-4">
                    <input class="field-input" name="name" value="{{ old('name', $user->name) }}" required>
                    <input class="field-input bg-slate-100" value="{{ $user->auth_provider === 'whatsapp' ? 'Registered with WhatsApp' : $user->email }}" disabled>
                    <div class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-4 text-sm font-bold text-slate-600">
                        Login method: <span class="text-slate-950">{{ strtoupper($user->auth_provider ?? 'email') }}</span>
                    </div>
                    <input class="field-input" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="WhatsApp number">
                    <input class="field-input" name="company_name" value="{{ old('company_name', $user->company_name) }}" placeholder="Company name">
                </div>
                <button class="btn-primary mt-5" type="submit">Save Profile</button>
            </form>

            <form class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8" method="POST" action="{{ route('member.password.update') }}" data-reveal>
                @csrf
                @method('PATCH')
                <p class="section-kicker"><x-icon name="lock" class="h-4 w-4" />Security</p>
                <h2 class="mt-2 text-2xl font-black text-slate-950">Change password</h2>
                <div class="mt-6 grid gap-4">
                    <input class="field-input" type="password" name="current_password" placeholder="Current password" required>
                    <input class="field-input" type="password" name="password" placeholder="New password" required>
                    <input class="field-input" type="password" name="password_confirmation" placeholder="Confirm new password" required>
                </div>
                <button class="btn-secondary mt-5" type="submit">Update Password</button>
            </form>
        </div>
    </section>
@endsection
