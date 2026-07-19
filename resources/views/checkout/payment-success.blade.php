@extends('layouts.app')

@section('title', 'Payment Status - Agape153')

@section('content')
    @php
        $isPaid = $order->payment_status === 'paid';
    @endphp

    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30" src="https://images.unsplash.com/photo-1554224155-1696413565d3?auto=format&fit=crop&w=1800&q=80" alt="Invoice payment status">
            <div class="absolute inset-0 bg-slate-950/72"></div>
        </div>
        <div class="agape-container relative py-12" data-reveal>
            <p class="section-kicker {{ $isPaid ? 'text-teal-200' : 'text-amber-200' }}">
                <x-icon name="{{ $isPaid ? 'check' : 'history' }}" class="h-4 w-4" />
                {{ $isPaid ? 'Payment Successful' : 'Payment Status' }}
            </p>
            <h1 class="mt-3 break-words text-4xl font-black leading-tight text-white sm:text-6xl">{{ $order->order_number }}</h1>
            <p class="mt-4 max-w-2xl leading-8 text-slate-200">
                @if ($isPaid)
                    Pembayaran berhasil. Invoice sudah ditandai paid dan tim Agape153 akan melanjutkan proses order.
                @else
                    Status pembayaran terbaru masih {{ strtoupper($order->payment_status) }}. Jika pembayaran baru selesai, gunakan refresh status.
                @endif
            </p>
        </div>
    </section>

    <section class="section-pad bg-[#f8faf9]">
        <div class="agape-container max-w-5xl">
            @if (! $isPaid && $paymentSyncError)
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800" data-reveal>
                    {{ $paymentSyncError }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
                <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" data-reveal>
                    <div class="border-b border-slate-100 p-5">
                        <p class="section-kicker"><x-icon name="orders" class="h-4 w-4" />Invoice Items</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">Purchased products</h2>
                    </div>
                    @foreach ($order->items as $item)
                        <div class="grid gap-4 border-b border-slate-100 p-5 md:grid-cols-[84px_1fr_130px_140px] md:items-center">
                            <img class="h-20 w-20 rounded-2xl object-cover" src="{{ $item->product_image_url ?: $item->product?->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $item->product_name }}">
                            <div>
                                <div class="font-black text-slate-950">{{ $item->product_name }}</div>
                                <div class="mt-1 text-sm text-slate-500">{{ $item->product_sku ?: 'SKU unavailable' }}</div>
                            </div>
                            <div class="text-sm font-bold text-slate-700">{{ $item->quantity }} {{ $item->unit }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</div>
                            <div class="font-black text-slate-950">Rp{{ number_format($item->line_total, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </section>

                <aside class="h-max rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" data-reveal>
                    <span class="grid h-14 w-14 place-items-center rounded-2xl {{ $isPaid ? 'bg-teal-700' : 'bg-amber-600' }} text-white">
                        <x-icon name="{{ $isPaid ? 'check' : 'history' }}" class="h-7 w-7" />
                    </span>
                    <dl class="mt-6 grid gap-4 text-sm">
                        <div class="flex flex-wrap justify-between gap-4 rounded-2xl bg-[#f8faf9] p-4">
                            <dt class="text-slate-500">Payment Status</dt>
                            <dd class="font-black {{ $isPaid ? 'text-teal-800' : 'text-amber-700' }}">{{ strtoupper($order->payment_status) }}</dd>
                        </div>
                        <div class="flex flex-wrap justify-between gap-4 rounded-2xl bg-[#f8faf9] p-4">
                            <dt class="text-slate-500">Total</dt>
                            <dd class="font-black text-slate-950">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</dd>
                        </div>
                        <div class="rounded-2xl bg-[#f8faf9] p-4">
                            <dt class="text-slate-500">Transaction ID</dt>
                            <dd class="mt-1 break-words font-black text-slate-950">{{ $order->midtrans_transaction_id ?: $order->payment_reference ?: '-' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 grid gap-3">
                        @if (! $isPaid)
                            <a class="btn-primary w-full" href="{{ route('checkout.payment-success', $order) }}">
                                <x-icon name="history" class="h-4 w-4" />
                                Refresh Payment Status
                            </a>
                        @endif
                        <a class="btn-primary w-full" href="{{ route('member.purchase-invoice', $order) }}">
                            <x-icon name="download" class="h-4 w-4" />
                            Download Invoice PDF
                        </a>
                        <a class="btn-secondary w-full" href="{{ route('member.purchase-detail', $order) }}">
                            <x-icon name="orders" class="h-4 w-4" />
                            Order Detail
                        </a>
                        <a class="btn-secondary w-full" href="{{ route('products.index') }}">Continue Shopping</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @if ($isPaid)
        <div id="payment-success-modal" class="fixed inset-0 z-50 grid place-items-center bg-slate-950/55 p-4">
            <div class="max-w-md rounded-3xl bg-white p-6 text-center shadow-2xl">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-teal-700 text-white">
                    <x-icon name="check" class="h-7 w-7" />
                </span>
                <h2 class="mt-4 text-2xl font-black text-slate-950">Pembayaran Berhasil</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Invoice {{ $order->order_number }} sudah ditandai paid.</p>
                <button class="btn-primary mt-5 w-full" type="button" onclick="document.getElementById('payment-success-modal').remove()">Lihat Invoice</button>
            </div>
        </div>
    @endif
@endsection
