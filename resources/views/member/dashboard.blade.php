@extends('layouts.app')

@section('title', 'Member Dashboard - Agape153')

@section('content')
    @php
        $paidCount = $orders->where('payment_status', 'paid')->count();
        $openCount = $orders->whereNotIn('status', ['completed', 'cancelled'])->count();
        $t = $siteText ?? [];
        $quoteLabel = $t['product.quote_label'] ?? 'Please contact or drop us email';
        $quoteHint = $t['product.quote_hint'] ?? 'Pricing depends on MOQ';
    @endphp

    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30" src="https://images.unsplash.com/photo-1532634922-8fe0b757fb13?auto=format&fit=crop&w=1800&q=80" alt="Member sourcing dashboard">
            <div class="absolute inset-0 bg-slate-950/72"></div>
        </div>
        <div class="agape-container relative grid gap-8 py-12 md:grid-cols-[1fr_360px] md:items-end">
            <div data-reveal>
                <p class="section-kicker text-amber-200"><x-icon name="dashboard" class="h-4 w-4" />Member Area</p>
                <h1 class="mt-3 text-4xl font-black leading-tight sm:text-6xl">Halo, {{ auth()->user()->name }}.</h1>
                <p class="mt-4 max-w-2xl leading-8 text-slate-200">Review orders, invoices, favorite products, and profile data from one place.</p>
            </div>
            <div class="grid gap-3 rounded-3xl border border-white/15 bg-white/10 p-5 backdrop-blur" data-reveal>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-slate-200">Recent orders</span>
                    <strong class="text-3xl font-black text-amber-200">{{ $orders->count() }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-slate-200">Paid invoices</span>
                    <strong class="text-3xl font-black text-teal-200">{{ $paidCount }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-slate-200">Open orders</span>
                    <strong class="text-3xl font-black text-sky-200">{{ $openCount }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#f8faf9]">
        <div class="agape-container">
            <div class="flex flex-wrap gap-3" data-reveal>
                <a class="btn-primary" href="{{ route('member.purchase-history') }}">
                    <x-icon name="history" class="h-4 w-4" />
                    Purchase History
                </a>
                <a class="btn-secondary" href="{{ route('member.profile') }}">
                    <x-icon name="user" class="h-4 w-4" />
                    Profile
                </a>
                <a class="btn-secondary" href="{{ route('products.index') }}">
                    <x-icon name="package" class="h-4 w-4" />
                    Catalog
                </a>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_0.9fr]">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" data-reveal>
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="section-kicker"><x-icon name="orders" class="h-4 w-4" />Purchase History</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950">Recent orders</h2>
                        </div>
                        <a class="text-sm font-black text-teal-700 hover:text-teal-900" href="{{ route('member.purchase-history') }}">View all</a>
                    </div>
                    <div class="mt-5 grid gap-3">
                        @forelse ($orders as $order)
                            <a class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-4 transition hover:-translate-y-0.5 hover:border-teal-200 hover:bg-teal-50" href="{{ route('member.purchase-detail', $order) }}">
                                <div class="flex flex-wrap justify-between gap-3">
                                    <strong class="break-words text-slate-950">{{ $order->order_number }}</strong>
                                    <span class="status-pill border-teal-200 bg-white text-teal-800">{{ $order->statusLabel() }}</span>
                                </div>
                                <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm">
                                    <span class="font-bold text-slate-600">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    <span class="font-black {{ $order->payment_status === 'paid' ? 'text-teal-700' : 'text-amber-700' }}">{{ strtoupper($order->payment_status) }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-6 text-sm text-slate-600">
                                Belum ada order.
                                <a class="mt-4 inline-flex font-black text-teal-700" href="{{ route('products.index') }}">Browse catalog</a>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" data-reveal>
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="section-kicker"><x-icon name="check" class="h-4 w-4" />Wishlist</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950">Favorite products</h2>
                        </div>
                    </div>
                    <div class="mt-5 grid gap-3">
                        @forelse ($wishlists as $wishlist)
                            @if ($wishlist->product)
                                <a class="flex gap-3 rounded-2xl border border-slate-200 bg-[#f8faf9] p-3 transition hover:-translate-y-0.5 hover:border-teal-200 hover:bg-teal-50" href="{{ route('products.show', $wishlist->product) }}">
                                    <img class="h-20 w-20 rounded-2xl object-cover" src="{{ $wishlist->product->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $wishlist->product->name }}">
                                    <div>
                                        <div class="font-black text-slate-950">{{ $wishlist->product->name }}</div>
                                        <div class="mt-1 text-sm font-black text-teal-800">{{ $quoteLabel }}</div>
                                        <div class="mt-1 text-xs font-bold text-slate-500">{{ $quoteHint }}</div>
                                        <div class="mt-2 text-xs font-bold text-slate-500">{{ $wishlist->product->stock_quantity }} {{ $wishlist->product->unit }} stock</div>
                                    </div>
                                </a>
                            @endif
                        @empty
                            <div class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-6 text-sm text-slate-600">
                                Belum ada wishlist.
                                <a class="mt-4 inline-flex font-black text-teal-700" href="{{ route('products.index') }}">Find products</a>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection
