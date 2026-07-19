@extends('layouts.app')

@section('title', 'Verify WhatsApp - Agape153')

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30" src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1800&q=80" alt="WhatsApp verification">
            <div class="absolute inset-0 bg-slate-950/76"></div>
        </div>
        <div class="agape-container relative grid gap-8 py-12 lg:grid-cols-[0.9fr_1.1fr] lg:items-center" data-reveal>
            <div>
                <p class="section-kicker text-amber-200"><x-icon name="shield" class="h-4 w-4" />WhatsApp Verification</p>
                <h1 class="mt-3 text-4xl font-black leading-tight sm:text-6xl">Masukkan OTP untuk mengaktifkan akun.</h1>
                <p class="mt-4 max-w-2xl leading-8 text-slate-200">Kode berlaku 10 menit. Setelah berhasil, akun langsung login dan siap dipakai checkout.</p>
            </div>

            <div class="rounded-3xl border border-white/15 bg-white p-6 text-slate-950 shadow-2xl md:p-8">
                <div class="flex items-center justify-between gap-4">
                    <x-logo />
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-teal-50 text-teal-700">
                        <x-icon name="phone" class="h-5 w-5" />
                    </span>
                </div>

                <form class="mt-8 grid gap-4" method="POST" action="{{ route('register.whatsapp.verify') }}">
                    @csrf
                    <label class="grid gap-2">
                        <span class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">WhatsApp Number</span>
                        <input class="field-input" name="phone" value="{{ old('phone', $phone) }}" placeholder="6281234567890" required>
                    </label>
                    <label class="grid gap-2">
                        <span class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">OTP Code</span>
                        <input class="field-input text-center text-2xl font-black tracking-[0.35em]" inputmode="numeric" maxlength="6" name="code" placeholder="000000" required>
                    </label>
                    <button class="btn-primary min-h-12" type="submit">
                        <x-icon name="check" class="h-4 w-4" />
                        Verify & Activate Account
                    </button>
                </form>

                <form class="mt-4" method="POST" action="{{ route('register.whatsapp.resend') }}">
                    @csrf
                    <input type="hidden" name="phone" value="{{ old('phone', $phone) }}">
                    <button class="btn-secondary w-full" type="submit">
                        <x-icon name="history" class="h-4 w-4" />
                        Resend OTP
                    </button>
                </form>

                <p class="mt-5 rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                    Jika provider WhatsApp belum dikonfigurasi, kode OTP tetap tersimpan di admin notification log untuk kebutuhan development.
                </p>
            </div>
        </div>
    </section>
@endsection
