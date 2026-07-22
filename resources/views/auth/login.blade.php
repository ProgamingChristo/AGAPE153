@extends('layouts.app')

@section('title', 'Login - Agape153')

@section('content')
    <section class="relative min-h-[calc(100vh-6rem)] overflow-hidden bg-[#eef8f6]">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-[0.18]" src="https://images.unsplash.com/photo-1523741543316-beb7fc7023d8?auto=format&fit=crop&w=1800&q=80" alt="Indonesian agriculture">
            <div class="absolute inset-0 bg-[linear-gradient(110deg,rgba(238,248,246,0.98),rgba(255,255,255,0.92)_46%,rgba(213,241,237,0.9))]"></div>
        </div>

        <div class="agape-container relative grid min-h-[calc(100vh-6rem)] items-center py-8">
            <div class="mx-auto grid w-full max-w-6xl items-center gap-8 lg:grid-cols-[1fr_480px]">
                <div class="hidden lg:block" data-reveal>
                    <x-logo />
                    <p class="mt-8 text-sm font-black uppercase tracking-[0.22em] text-teal-700">Buyer Center</p>
                    <h1 class="mt-3 max-w-xl text-5xl font-black leading-tight text-slate-950">Satu akun untuk order, invoice, tracking, dan repeat purchase.</h1>
                    <div class="mt-8 grid max-w-xl grid-cols-3 gap-3">
                        @foreach ([['icon' => 'cart', 'label' => 'Cart'], ['icon' => 'download', 'label' => 'Invoice'], ['icon' => 'truck', 'label' => 'Shipping']] as $item)
                            <div class="rounded-2xl border border-white/80 bg-white/75 p-4 shadow-sm backdrop-blur">
                                <x-icon :name="$item['icon']" class="h-5 w-5 text-teal-700" />
                                <div class="mt-3 text-sm font-black text-slate-800">{{ $item['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mx-auto w-full max-w-[480px] rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_22px_70px_rgba(15,23,42,0.16)] md:p-10" data-reveal>
                    <div class="mb-7 flex items-center justify-between gap-4">
                        <div>
                            <div class="lg:hidden">
                                <x-logo variant="compact" />
                            </div>
                            <h2 class="mt-4 text-2xl font-black text-slate-950 lg:mt-0">Masuk ke Agape153</h2>
                        </div>
                        <a class="rounded-full px-3 py-2 text-sm font-black text-teal-700 transition hover:bg-teal-50" href="{{ route('register') }}">Daftar</a>
                    </div>

                    <a class="inline-flex h-[58px] w-full items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white px-5 text-base font-black text-slate-700 transition hover:-translate-y-0.5 hover:border-teal-400 hover:text-teal-700 hover:shadow-lg" href="{{ route('auth.google.redirect') }}">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-white text-xl font-black text-[#4285f4] shadow-sm">G</span>
                        Masuk dengan Google
                    </a>

                    <div class="my-7 flex items-center gap-5 text-sm font-semibold text-slate-500">
                        <span class="h-px flex-1 bg-slate-200"></span>
                        atau masuk dengan
                        <span class="h-px flex-1 bg-slate-200"></span>
                    </div>

                    <form class="grid gap-4" method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <div>
                            <label class="mb-2 block text-sm font-black text-slate-700" for="login">Email atau Nomor WhatsApp</label>
                            <input id="login" class="h-[58px] w-full rounded-xl border border-slate-300 bg-white px-4 text-base font-semibold text-slate-900 outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:bg-teal-50/40 focus:ring-4 focus:ring-teal-100" name="login" value="{{ old('login') }}" placeholder="08123456789 / buyer@email.com" required autofocus>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-black text-slate-700" for="password">Password</label>
                            <input id="password" class="h-[58px] w-full rounded-xl border border-slate-300 bg-white px-4 text-base font-semibold text-slate-900 outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:bg-teal-50/40 focus:ring-4 focus:ring-teal-100" type="password" name="password" placeholder="Masukkan password" required>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                                <input class="h-4 w-4 rounded border-slate-300 text-teal-700 focus:ring-teal-600" type="checkbox" name="remember" value="1">
                                Ingat saya
                            </label>
                            <a class="text-sm font-black text-teal-700 hover:text-teal-900" href="{{ route('password.request') }}">Lupa password?</a>
                        </div>

                        <button class="mt-1 inline-flex h-[58px] w-full items-center justify-center gap-2 rounded-xl bg-slate-950 px-5 text-base font-black text-white shadow-[0_16px_34px_rgba(15,23,42,0.2)] transition hover:-translate-y-0.5 hover:bg-teal-700" type="submit">
                            <x-icon name="login" class="h-5 w-5" />
                            Masuk
                        </button>
                    </form>

                    <p class="mt-7 rounded-2xl bg-teal-50 px-4 py-3 text-sm leading-6 text-slate-600">
                        Belum punya akun?
                        <a class="font-black text-teal-700 hover:text-teal-900" href="{{ route('register') }}">Daftar dengan Google, email, atau WhatsApp OTP</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
