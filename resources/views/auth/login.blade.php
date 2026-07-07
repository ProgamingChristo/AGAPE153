@extends('layouts.app')

@section('title', 'Login - Agape153')

@section('content')
    <section class="grid min-h-[calc(100vh-4rem)] bg-white lg:grid-cols-2">
        <div class="hidden bg-[#edf7f4] lg:block">
            <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1200&q=80" alt="Coffee beans">
        </div>
        <div class="flex items-center px-6 py-12">
            <div class="mx-auto w-full max-w-md">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Member Login</p>
                <h1 class="mt-3 text-4xl font-black text-slate-950">Masuk ke akun.</h1>
                <form class="mt-8 grid gap-4" method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <input class="rounded-xl border border-slate-200 px-4 py-3" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
                    <input class="rounded-xl border border-slate-200 px-4 py-3" type="password" name="password" placeholder="Password" required>
                    <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
                        <input type="checkbox" name="remember" value="1">
                        Remember me
                    </label>
                    <button class="btn-primary" type="submit">Login</button>
                </form>
                <p class="mt-5 text-sm text-slate-600">Belum punya akun? <a class="font-black text-teal-700" href="{{ route('register') }}">Register</a></p>
            </div>
        </div>
    </section>
@endsection
