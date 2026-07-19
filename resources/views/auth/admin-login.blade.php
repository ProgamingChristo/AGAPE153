@extends('layouts.app')

@section('title', 'Admin Login - Agape153')
@section('description', 'Secure administrator login for Agape153.')

@section('content')
    <section class="relative min-h-[calc(100vh-4rem)] overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-25" src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1800&q=80" alt="Export logistics">
        </div>
        <div class="agape-container relative grid min-h-[calc(100vh-4rem)] items-center gap-10 py-12 lg:grid-cols-[1fr_0.9fr]">
            <div class="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-black text-teal-100">
                    <x-icon name="shield" class="h-4 w-4" />
                    Secure Admin Access
                </div>
                <h1 class="mt-6 break-words text-4xl font-black leading-tight sm:text-6xl">Agape153 Operations Console</h1>
                <p class="mt-5 max-w-xl text-lg leading-8 text-slate-200">Dedicated login for catalog, order, and business operations. Admin sessions are protected with a server-side session token and idle timeout.</p>
                <div class="mt-8 inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-3 text-sm font-bold text-slate-100">
                    <x-icon name="history" class="h-4 w-4 text-teal-200" />
                    Auto logout after {{ $timeoutMinutes }} minutes idle
                </div>
            </div>

            <div class="soft-panel fade-up rounded-2xl p-6 text-slate-950">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <x-logo class="mb-5" />
                        <p class="text-sm font-black uppercase tracking-[0.2em] text-teal-700">Administrator</p>
                        <h2 class="mt-2 text-3xl font-black">Sign in</h2>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-teal-700 text-white">
                        <x-icon name="lock" class="h-6 w-6" />
                    </span>
                </div>

                <form class="mt-8 grid gap-4" method="POST" action="{{ route('admin.login.store') }}">
                    @csrf
                    <label class="grid gap-2 text-sm font-bold text-slate-700">
                        Email
                        <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-teal-500" type="email" name="email" value="{{ old('email') }}" placeholder="admin@agape153.com" required autofocus>
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">
                        Password
                        <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-teal-500" type="password" name="password" placeholder="Password" required>
                    </label>
                    <button class="btn-primary mt-2" type="submit">
                        <x-icon name="login" class="h-4 w-4" />
                        Enter Admin
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
