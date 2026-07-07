@extends('layouts.app')

@section('title', 'Order Success - Agape153')

@section('content')
    <section class="bg-white py-12">
        <div class="agape-container max-w-3xl">
            <div class="rounded-2xl border border-teal-200 bg-teal-50 p-8">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Order Created</p>
                <h1 class="mt-3 text-4xl font-black text-slate-950">{{ $order->order_number }}</h1>
                <p class="mt-4 text-slate-600">Order berhasil dibuat. Lanjutkan konfirmasi ke WhatsApp agar tim sales memproses harga final, stok, dan pengiriman.</p>
                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-slate-500">Tracking Code</dt>
                        <dd class="font-black text-slate-950">{{ $order->tracking_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">Total</dt>
                        <dd class="font-black text-slate-950">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</dd>
                    </div>
                </dl>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a class="btn-primary" href="{{ $order->wa_checkout_url }}" target="_blank" rel="noopener">Confirm via WhatsApp</a>
                    <a class="btn-secondary" href="{{ route('orders.track') }}">Track Order</a>
                </div>
            </div>
        </div>
    </section>
@endsection
