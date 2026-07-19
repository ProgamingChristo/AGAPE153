@extends('layouts.app')

@section('title', 'Forgot Password - Agape153')

@section('content')
    <section class="relative min-h-[calc(100vh-6rem)] overflow-hidden bg-slate-950">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-35" src="https://images.unsplash.com/photo-1494412651409-8963ce7935a7?auto=format&fit=crop&w=1800&q=80" alt="Password reset">
            <div class="absolute inset-0 bg-slate-950/72"></div>
        </div>
        <div class="agape-container relative grid min-h-[calc(100vh-6rem)] items-center gap-8 py-12 lg:grid-cols-[1fr_440px]">
            <div class="text-white" data-reveal>
                <x-logo />
                <p class="section-kicker mt-8 text-amber-200"><x-icon name="lock" class="h-4 w-4" />Password Recovery</p>
                <h1 class="mt-3 max-w-3xl text-4xl font-black leading-tight sm:text-6xl">Reset password lewat email.</h1>
                <p class="mt-5 max-w-2xl leading-8 text-slate-200">Masukkan email akunmu. Sistem akan mengirim link reset password yang aman dan hanya berlaku sementara.</p>
            </div>

            <form class="rounded-3xl border border-white/15 bg-white p-6 shadow-2xl md:p-8" method="POST" action="{{ route('password.email') }}" data-reveal>
                @csrf
                <h2 class="text-2xl font-black text-slate-950">Forgot password</h2>
                <div class="mt-6 grid gap-4">
                    <input class="field-input" type="email" name="email" value="{{ old('email') }}" placeholder="Email address" required autofocus>
                    <button class="btn-primary" type="submit">
                        <x-icon name="mail" class="h-4 w-4" />
                        Send Reset Link
                    </button>
                    <a class="btn-secondary" href="{{ route('login') }}">Back to Login</a>
                </div>
            </form>
        </div>
    </section>
@endsection
