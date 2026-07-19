@extends('layouts.app')

@section('title', 'Order Tracking - Agape153')

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30" src="https://images.unsplash.com/photo-1494412651409-8963ce7935a7?auto=format&fit=crop&w=1800&q=80" alt="Order tracking logistics">
            <div class="absolute inset-0 bg-slate-950/72"></div>
        </div>
        <div class="agape-container relative py-12" data-reveal>
            <p class="section-kicker text-amber-200"><x-icon name="search" class="h-4 w-4" />Order Tracking</p>
            <h1 class="mt-3 max-w-4xl text-4xl font-black leading-tight sm:text-6xl">Track order, payment, and shipping progress.</h1>
            <p class="mt-4 max-w-2xl leading-8 text-slate-200">Use order number or tracking code from your checkout/invoice page.</p>
        </div>
    </section>

    <section class="section-pad bg-[#f8faf9]">
        <div class="agape-container max-w-4xl">
            <form class="grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_140px]" method="POST" action="{{ route('orders.track.search') }}" data-reveal>
                @csrf
                <input class="field-input" name="keyword" value="{{ old('keyword') }}" placeholder="Order number or tracking code" required>
                <button class="btn-primary" type="submit">
                    <x-icon name="search" class="h-4 w-4" />
                    Search
                </button>
            </form>

            @isset($order)
                @if ($order)
                    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" data-reveal>
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                            <div>
                                <div class="text-sm font-bold text-slate-500">Order Number</div>
                                <div class="mt-1 break-words text-3xl font-black text-slate-950">{{ $order->order_number }}</div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="status-pill border-teal-200 bg-teal-50 text-teal-800">{{ $order->statusLabel() }}</span>
                                <span class="status-pill {{ $order->payment_status === 'paid' ? 'border-teal-200 bg-teal-50 text-teal-800' : 'border-amber-200 bg-amber-50 text-amber-800' }}">{{ strtoupper($order->payment_status) }}</span>
                                @if ($order->shipping_provider || $order->shipping_status)
                                    <span class="status-pill">{{ $order->shipping_provider ?: 'Shipping' }}{{ $order->shipping_status ? ' / '.$order->shipping_status : '' }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl bg-[#f8faf9] p-4">
                                <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Tracking Code</div>
                                <div class="mt-1 font-black text-slate-950">{{ $order->tracking_code ?: '-' }}</div>
                            </div>
                            <div class="rounded-2xl bg-[#f8faf9] p-4">
                                <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Total</div>
                                <div class="mt-1 font-black text-teal-800">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            </div>
                            <div class="rounded-2xl bg-[#f8faf9] p-4">
                                <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Created</div>
                                <div class="mt-1 font-black text-slate-950">{{ $order->created_at->format('d M Y') }}</div>
                            </div>
                        </div>

                        <div class="mt-6 rounded-2xl border border-slate-200 p-5">
                            <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                                <div>
                                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Shipment Flow</div>
                                    <div class="mt-1 text-xl font-black text-slate-950">{{ $order->shippingStatusLabel() }}</div>
                                </div>
                                <div class="font-bold text-slate-600">{{ $order->shipping_provider ?: 'Agape153' }}</div>
                            </div>
                            <div class="mt-5 grid gap-4">
                                @foreach ($order->shipmentTimeline() as $step)
                                    <div class="grid grid-cols-[32px_1fr] gap-3">
                                        <div class="grid h-8 w-8 place-items-center rounded-full border {{ $step['is_done'] ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-200 bg-white text-slate-300' }}">
                                            <x-icon name="{{ $step['is_done'] ? 'check' : 'history' }}" class="h-4 w-4" />
                                        </div>
                                        <div class="border-b border-slate-100 pb-4">
                                            <div class="font-black {{ $step['is_current'] ? 'text-teal-800' : 'text-slate-950' }}">{{ $step['title'] }}</div>
                                            <p class="mt-1 text-sm leading-6 text-slate-500">{{ $step['description'] }}</p>
                                            @if ($step['time'])
                                                <div class="mt-1 text-xs font-bold text-slate-400">{{ $step['time']->format('d M Y H:i') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200">
                            @foreach ($order->items as $item)
                                <div class="flex justify-between gap-4 border-b border-slate-100 p-4 text-sm">
                                    <span class="font-bold text-slate-800">{{ $item->product_name }} x {{ $item->quantity }}</span>
                                    <strong class="text-slate-950">Rp{{ number_format($item->line_total, 0, ',', '.') }}</strong>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            @if ($order->tracking_url)
                                <a class="btn-primary" href="{{ $order->tracking_url }}" target="_blank" rel="noopener">
                                    <x-icon name="truck" class="h-4 w-4" />
                                    Open Logistic Tracking
                                </a>
                            @endif
                            <a class="btn-secondary" href="{{ route('products.index') }}">Browse More Products</a>
                        </div>
                    </div>
                @else
                    <div class="mt-8 rounded-3xl border border-amber-200 bg-amber-50 p-6 font-bold text-amber-800" data-reveal>
                        Order tidak ditemukan. Periksa kembali nomor order atau tracking code.
                    </div>
                @endif
            @endisset
        </div>
    </section>
@endsection
