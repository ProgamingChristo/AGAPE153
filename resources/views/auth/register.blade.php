@extends('layouts.app')

@section('title', 'Register - Agape153')

@section('content')
    <section class="relative min-h-[calc(100vh-6rem)] overflow-hidden bg-[#eef8f6]">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-[0.18]" src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1800&q=80" alt="Buyer registration">
            <div class="absolute inset-0 bg-[linear-gradient(110deg,rgba(238,248,246,0.98),rgba(255,255,255,0.94)_48%,rgba(214,240,235,0.9))]"></div>
        </div>

        <div class="agape-container relative grid min-h-[calc(100vh-6rem)] items-center py-8">
            <div class="mx-auto w-full max-w-6xl">
                <div class="mb-7 flex flex-wrap items-center justify-between gap-4" data-reveal>
                    <div>
                        <x-logo variant="compact" />
                        <h1 class="mt-5 text-3xl font-black text-slate-950 md:text-4xl">Daftar akun pembeli Agape153</h1>
                        <p class="mt-2 max-w-2xl text-slate-600">Pilih Google untuk cara tercepat, atau daftar manual dengan email/WhatsApp.</p>
                    </div>
                    <a class="rounded-full bg-white px-5 py-3 text-sm font-black text-teal-700 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg" href="{{ route('login') }}">Sudah punya akun?</a>
                </div>

                <div class="grid gap-6 lg:grid-cols-[420px_1fr]" data-reveal>
                    <aside class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.12)] md:p-8">
                        <h2 class="text-2xl font-black text-slate-950">Daftar cepat</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Akun akan dipakai untuk cart, invoice PDF, purchase history, tracking, dan review produk.</p>

                        <a class="mt-6 inline-flex h-[58px] w-full items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white px-5 text-base font-black text-slate-700 transition hover:-translate-y-0.5 hover:border-teal-400 hover:text-teal-700 hover:shadow-lg" href="{{ route('auth.google.redirect') }}">
                            <span class="grid h-7 w-7 place-items-center rounded-full bg-white text-xl font-black text-[#4285f4] shadow-sm">G</span>
                            Lanjut dengan Google
                        </a>

                        <div class="mt-7 grid gap-3">
                            @foreach ([['icon' => 'phone', 'label' => 'WhatsApp OTP'], ['icon' => 'mail', 'label' => 'Email Login'], ['icon' => 'download', 'label' => 'Invoice PDF'], ['icon' => 'truck', 'label' => 'Order Tracking']] as $item)
                                <div class="flex items-center gap-3 rounded-2xl bg-slate-50 p-4 font-bold text-slate-700">
                                    <span class="grid h-9 w-9 place-items-center rounded-full bg-white text-teal-700 shadow-sm">
                                        <x-icon :name="$item['icon']" class="h-4 w-4" />
                                    </span>
                                    {{ $item['label'] }}
                                </div>
                            @endforeach
                        </div>
                    </aside>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.12)] md:p-8">
                        <div class="flex items-center gap-5 text-sm font-semibold text-slate-500">
                            <span class="h-px flex-1 bg-slate-200"></span>
                            atau daftar manual
                            <span class="h-px flex-1 bg-slate-200"></span>
                        </div>

                        <div class="mt-7 grid gap-7 xl:grid-cols-2">
                            <form class="grid gap-4" method="POST" action="{{ route('register.whatsapp') }}">
                                @csrf
                                <div>
                                    <p class="text-lg font-black text-slate-950">WhatsApp OTP</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">Verifikasi nomor WhatsApp sebelum akun aktif.</p>
                                </div>
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required>
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" name="phone" value="{{ old('phone') }}" placeholder="Nomor WhatsApp" required>
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" name="company_name" value="{{ old('company_name') }}" placeholder="Nama perusahaan">
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" type="password" name="password" placeholder="Password" required>
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" type="password" name="password_confirmation" placeholder="Konfirmasi password" required>
                                <button class="inline-flex h-[56px] items-center justify-center gap-2 rounded-xl bg-teal-700 px-5 font-black text-white shadow-[0_14px_30px_rgba(15,118,110,0.22)] transition hover:-translate-y-0.5 hover:bg-slate-950" type="submit">
                                    <x-icon name="phone" class="h-5 w-5" />
                                    Kirim OTP WhatsApp
                                </button>
                            </form>

                            <form class="grid gap-4 border-t border-slate-200 pt-7 xl:border-l xl:border-t-0 xl:pl-7 xl:pt-0" method="POST" action="{{ route('register.store') }}">
                                @csrf
                                <div>
                                    <p class="text-lg font-black text-slate-950">Email & Password</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">Gunakan email aktif untuk reset password dan notifikasi.</p>
                                </div>
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required>
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" name="phone" value="{{ old('phone') }}" placeholder="Nomor WhatsApp">
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" name="company_name" value="{{ old('company_name') }}" placeholder="Nama perusahaan">
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" type="password" name="password" placeholder="Password" required>
                                <input class="h-[56px] rounded-xl border border-slate-300 px-4 font-semibold outline-none transition placeholder:text-slate-300 focus:border-teal-500 focus:ring-4 focus:ring-teal-100" type="password" name="password_confirmation" placeholder="Konfirmasi password" required>
                                <button class="inline-flex h-[56px] items-center justify-center gap-2 rounded-xl bg-slate-950 px-5 font-black text-white shadow-[0_14px_30px_rgba(15,23,42,0.18)] transition hover:-translate-y-0.5 hover:bg-teal-700" type="submit">
                                    <x-icon name="mail" class="h-5 w-5" />
                                    Daftar Email
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
