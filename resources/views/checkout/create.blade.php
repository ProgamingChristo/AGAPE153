@extends('layouts.app')

@section('title', 'Checkout - Agape153')

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30" src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=1800&q=80" alt="Checkout commodities">
            <div class="absolute inset-0 bg-slate-950/72"></div>
        </div>
        <div class="agape-container relative py-12" data-reveal>
            <p class="section-kicker text-amber-200"><x-icon name="lock" class="h-4 w-4" />Checkout</p>
            <h1 class="mt-3 max-w-4xl text-4xl font-black leading-tight sm:text-6xl">Complete buyer details and choose payment path.</h1>
            <p class="mt-4 max-w-2xl leading-8 text-slate-200">Use Online payment popup, or WhatsApp for direct confirmation with the Agape153 team.</p>
        </div>
    </section>

    <section class="section-pad bg-[#f8faf9]">
        <div class="agape-container grid gap-8 lg:grid-cols-[1fr_380px]">
            <div data-reveal>
                <form class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8" action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    <div class="flex items-center gap-3">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-teal-700 text-white">
                            <x-icon name="user" class="h-6 w-6" />
                        </span>
                        <div>
                            <p class="section-kicker">Buyer Information</p>
                            <h2 class="text-2xl font-black text-slate-950">Order recipient and destination</h2>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <input class="field-input" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" placeholder="Full name" required>
                        <input class="field-input" type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" placeholder="Email">
                        <input class="field-input" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" placeholder="WhatsApp number" required>
                        <input class="field-input" name="company_name" value="{{ old('company_name', auth()->user()->company_name ?? '') }}" placeholder="Company name">
                        <input class="field-input md:col-span-2" name="country" value="{{ old('country', 'Indonesia') }}" placeholder="Country" required>
                        <textarea class="field-input min-h-32 md:col-span-2" name="shipping_address" placeholder="Shipping address" required>{{ old('shipping_address') }}</textarea>
                        <textarea class="field-input min-h-24 md:col-span-2" name="notes" placeholder="Notes, target quantity, export destination, or packaging request">{{ old('notes') }}</textarea>
                    </div>

                    <div class="mt-7">
                        <p class="section-kicker"><x-icon name="lock" class="h-4 w-4" />Payment Method</p>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <button class="btn-primary min-h-16 flex-col items-start px-5 text-left sm:items-start" type="submit" name="payment_method" value="midtrans">
                                <span class="inline-flex items-center gap-2">
                                    <x-icon name="lock" class="h-4 w-4" />
                                    Bayar Online
                                </span>
                                <span class="text-xs font-semibold text-white/80">Popup payment opens automatically</span>
                            </button>
                            <button class="btn-secondary min-h-16 flex-col items-start px-5 text-left" type="submit" name="payment_method" value="whatsapp">
                                <span class="inline-flex items-center gap-2">
                                    <x-icon name="phone" class="h-4 w-4" />
                                    Konfirmasi via WhatsApp
                                </span>
                                <span class="text-xs font-semibold text-slate-500">Redirect to Agape153 sales team</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <aside class="h-max rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" data-reveal>
                <p class="section-kicker"><x-icon name="orders" class="h-4 w-4" />Order Summary</p>
                <div class="mt-5 grid gap-4">
                    @foreach ($lines as $line)
                        <div class="flex gap-3 border-b border-slate-100 pb-4">
                            <img class="h-16 w-16 rounded-2xl object-cover" src="{{ $line['product']->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $line['product']->name }}">
                            <div class="min-w-0 flex-1">
                                <div class="font-black text-slate-950">{{ $line['product']->name }}</div>
                                <div class="mt-1 text-sm text-slate-500">{{ $line['quantity'] }} {{ $line['product']->unit }} x {{ $line['product']->formattedPrice() }}</div>
                            </div>
                            <div class="font-black text-slate-950">Rp{{ number_format($line['line_total'], 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 flex justify-between rounded-2xl bg-[#f8faf9] p-4 text-lg font-black">
                    <span class="text-slate-950">Total</span>
                    <span class="text-teal-800">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="mt-5 grid gap-3 text-sm leading-6 text-slate-600">
                    <div class="flex gap-3">
                        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-teal-700" />
                        Invoice PDF is available after order creation.
                    </div>
                    <div class="flex gap-3">
                        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-teal-700" />
                        Payment status is synced with the online payment gateway when available.
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
