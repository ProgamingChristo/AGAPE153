@extends('layouts.app')

@section('title', 'Purchase History - Agape153')

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30" src="https://images.unsplash.com/photo-1494412685616-a5d310fbb07d?auto=format&fit=crop&w=1800&q=80" alt="Invoices and trading documents">
            <div class="absolute inset-0 bg-slate-950/72"></div>
        </div>
        <div class="agape-container relative py-12" data-reveal>
            <p class="section-kicker text-amber-200"><x-icon name="orders" class="h-4 w-4" />Invoice</p>
            <div class="mt-3 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <h1 class="text-4xl font-black text-white sm:text-6xl">Purchase history.</h1>
                    <p class="mt-4 max-w-2xl leading-8 text-slate-200">Review submitted orders, tracking code, payment status, and invoice download access.</p>
                </div>
                <a class="btn-secondary text-slate-950" href="{{ route('member.dashboard') }}">
                    <x-icon name="dashboard" class="h-4 w-4" />
                    Dashboard
                </a>
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#f8faf9]">
        <div class="agape-container">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" data-reveal>
                @forelse ($orders as $order)
                    <a class="grid gap-4 border-b border-slate-100 p-5 transition hover:bg-teal-50 lg:grid-cols-[1fr_170px_180px_160px_150px] lg:items-center" href="{{ route('member.purchase-detail', $order) }}">
                        <div>
                            <div class="break-words text-lg font-black text-slate-950">{{ $order->order_number }}</div>
                            <div class="mt-1 text-sm text-slate-500">{{ $order->created_at->format('d M Y H:i') }} / {{ $order->items_count }} item</div>
                        </div>
                        <div>
                            <div class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Tracking</div>
                            <div class="mt-1 font-bold text-slate-800">{{ $order->tracking_code ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Shipment</div>
                            <div class="mt-1 font-bold text-teal-800">{{ $order->shippingStatusLabel() }}</div>
                        </div>
                        <div class="flex flex-wrap gap-2 lg:block">
                            <span class="status-pill border-teal-200 bg-teal-50 text-teal-800">{{ $order->statusLabel() }}</span>
                            <div class="mt-2 text-xs font-black {{ $order->payment_status === 'paid' ? 'text-teal-700' : 'text-amber-700' }}">{{ strtoupper($order->payment_status) }}</div>
                        </div>
                        <div class="text-lg font-black text-slate-950">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</div>
                    </a>
                @empty
                    <div class="p-8 text-slate-600">
                        <div class="text-xl font-black text-slate-950">No purchase history yet.</div>
                        <p class="mt-2 text-sm leading-6">Once you create an order, invoices and payment status will appear here.</p>
                        <a class="btn-primary mt-5" href="{{ route('products.index') }}">
                            <x-icon name="package" class="h-4 w-4" />
                            Browse Catalog
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $orders->links() }}</div>
        </div>
    </section>
@endsection
