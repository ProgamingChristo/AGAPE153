@extends('layouts.app')

@section('title', 'Register - Agape153')

@section('content')
    <section class="bg-white py-12">
        <div class="agape-container max-w-2xl">
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Member Registration</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Buat akun pembeli.</h1>
            <form class="mt-8 grid gap-4 rounded-2xl border border-slate-200 bg-[#f8faf9] p-6" method="POST" action="{{ route('register.store') }}">
                @csrf
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="name" value="{{ old('name') }}" placeholder="Full name" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="phone" value="{{ old('phone') }}" placeholder="WhatsApp number">
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="company_name" value="{{ old('company_name') }}" placeholder="Company name">
                <input class="rounded-xl border border-slate-200 px-4 py-3" type="password" name="password" placeholder="Password" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" type="password" name="password_confirmation" placeholder="Confirm password" required>
                <button class="btn-primary" type="submit">Register</button>
            </form>
        </div>
    </section>
@endsection
