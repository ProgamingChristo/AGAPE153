@extends('layouts.app')

@section('title', 'Checkout - Agape153')

@section('content')
    <section class="bg-white py-12">
        <div class="agape-container grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Checkout</p>
                <h1 class="mt-3 text-4xl font-black text-slate-950">Order manual via WhatsApp.</h1>
                <form class="mt-8 grid gap-4" action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    <input class="rounded-xl border border-slate-200 px-4 py-3" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" placeholder="Full name" required>
                    <input class="rounded-xl border border-slate-200 px-4 py-3" type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" placeholder="Email">
                    <input class="rounded-xl border border-slate-200 px-4 py-3" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" placeholder="WhatsApp number" required>
                    <input class="rounded-xl border border-slate-200 px-4 py-3" name="company_name" value="{{ old('company_name', auth()->user()->company_name ?? '') }}" placeholder="Company name">
                    <input class="rounded-xl border border-slate-200 px-4 py-3" name="country" value="{{ old('country', 'Indonesia') }}" placeholder="Country" required>
                    <textarea class="min-h-32 rounded-xl border border-slate-200 px-4 py-3" name="shipping_address" placeholder="Shipping address" required>{{ old('shipping_address') }}</textarea>
                    <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3" name="notes" placeholder="Notes, target quantity, export destination, or packaging request">{{ old('notes') }}</textarea>
                    <button class="btn-primary" type="submit">Create Order</button>
                </form>
            </div>
            <aside class="soft-panel h-max rounded-2xl p-6">
                <h2 class="text-xl font-black text-slate-950">Order Summary</h2>
                <div class="mt-5 grid gap-4">
                    @foreach ($lines as $line)
                        <div class="flex justify-between gap-4 border-b border-slate-100 pb-3 text-sm">
                            <div>
                                <div class="font-bold text-slate-950">{{ $line['product']->name }}</div>
                                <div class="text-slate-500">{{ $line['quantity'] }} {{ $line['product']->unit }}</div>
                            </div>
                            <div class="font-black text-slate-950">Rp{{ number_format($line['line_total'], 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 flex justify-between text-lg font-black text-teal-800">
                    <span>Total</span>
                    <span>Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
            </aside>
        </div>
    </section>
@endsection
