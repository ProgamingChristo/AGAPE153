@extends('layouts.app')

@section('title', $order->order_number.' - Purchase Detail')

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30" src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=1800&q=80" alt="Purchase invoice detail">
            <div class="absolute inset-0 bg-slate-950/72"></div>
        </div>
        <div class="agape-container relative py-12" data-reveal>
            <p class="section-kicker text-amber-200"><x-icon name="orders" class="h-4 w-4" />Purchase Detail</p>
            <div class="mt-3 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <h1 class="break-words text-4xl font-black leading-tight text-white sm:text-6xl">{{ $order->order_number }}</h1>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="status-pill border-teal-200 bg-teal-50 text-teal-800">{{ $order->statusLabel() }}</span>
                        <span class="status-pill {{ $order->payment_status === 'paid' ? 'border-teal-200 bg-teal-50 text-teal-800' : 'border-amber-200 bg-amber-50 text-amber-800' }}">{{ strtoupper($order->payment_status) }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a class="btn-primary" href="{{ route('member.purchase-invoice', $order) }}">
                        <x-icon name="download" class="h-4 w-4" />
                        Download PDF
                    </a>
                    <a class="btn-secondary text-slate-950" href="{{ route('member.purchase-history') }}">
                        <x-icon name="history" class="h-4 w-4" />
                        Back
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#f8faf9]">
        <div class="agape-container grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="grid gap-6">
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" data-reveal>
                    <div class="border-b border-slate-100 bg-white p-5">
                        <p class="section-kicker"><x-icon name="package" class="h-4 w-4" />Ordered Products</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">Invoice items</h2>
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
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" data-reveal>
                    <p class="section-kicker"><x-icon name="truck" class="h-4 w-4" />Shipment Flow</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">{{ $order->shippingStatusLabel() }}</h2>
                    <div class="mt-6 grid gap-4">
                        @foreach ($order->shipmentTimeline() as $step)
                            <div class="grid grid-cols-[34px_1fr] gap-3">
                                <div class="grid h-9 w-9 place-items-center rounded-full border {{ $step['is_done'] ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-200 bg-white text-slate-300' }}">
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

                    @if ($order->canCustomerComplete())
                        <form class="mt-6 rounded-2xl border border-teal-200 bg-teal-50 p-4" method="POST" action="{{ route('member.purchase-complete', $order) }}">
                            @csrf
                            @method('PATCH')
                            <div class="font-black text-slate-950">Paket sudah sampai?</div>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Klik selesai jika barang sudah diterima dengan baik. Setelah itu kamu bisa memberi rating produk.</p>
                            <button class="btn-primary mt-4" type="submit">
                                <x-icon name="check" class="h-4 w-4" />
                                Selesaikan Pesanan
                            </button>
                        </form>
                    @endif
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" data-reveal>
                    <p class="section-kicker"><x-icon name="star" class="h-4 w-4" />Product Review</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">Rating and comments.</h2>
                    <div class="mt-5 grid gap-4">
                        @foreach ($order->items as $item)
                            <div class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-4">
                                <div class="font-black text-slate-950">{{ $item->product_name }}</div>
                                @if ($item->review)
                                    <div class="mt-3 flex items-center gap-2 text-amber-500">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <x-icon name="star" class="h-4 w-4 {{ $i <= $item->review->rating ? 'text-amber-500' : 'text-slate-300' }}" />
                                        @endfor
                                        <span class="text-sm font-black text-slate-950">{{ $item->review->rating }}/5</span>
                                    </div>
                                    <p class="mt-3 rounded-xl bg-white p-4 text-sm leading-6 text-slate-600">{{ $item->review->comment ?: 'No comment.' }}</p>
                                    @if ($item->review->admin_reply)
                                        <div class="mt-3 rounded-xl border border-teal-200 bg-teal-50 p-4 text-sm leading-6 text-teal-900">
                                            <div class="font-black">Agape153 reply</div>
                                            <p class="mt-1">{{ $item->review->admin_reply }}</p>
                                        </div>
                                    @endif
                                @elseif ($order->canBeReviewed())
                                    <form class="mt-4 grid gap-3" method="POST" action="{{ route('member.product-review.store', $item) }}">
                                        @csrf
                                        <label class="grid gap-2 text-sm font-bold text-slate-700">Rating
                                            <select class="field-input" name="rating" required>
                                                <option value="">Pilih rating</option>
                                                @for ($rating = 5; $rating >= 1; $rating--)
                                                    <option value="{{ $rating }}">{{ $rating }} / 5</option>
                                                @endfor
                                            </select>
                                        </label>
                                        <label class="grid gap-2 text-sm font-bold text-slate-700">Komentar
                                            <textarea class="field-input min-h-28" name="comment" placeholder="Ceritakan kualitas produk, packing, dan pengalaman pembelian."></textarea>
                                        </label>
                                        <button class="btn-primary w-full sm:w-max" type="submit">
                                            <x-icon name="star" class="h-4 w-4" />
                                            Kirim Review
                                        </button>
                                    </form>
                                @else
                                    <div class="mt-3 rounded-xl bg-white p-4 text-sm font-semibold text-slate-500">Review tersedia setelah pesanan selesai.</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <aside class="h-max rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" data-reveal>
                <p class="section-kicker"><x-icon name="orders" class="h-4 w-4" />Order Summary</p>
                <dl class="mt-5 grid gap-4 text-sm">
                    <div class="flex justify-between gap-4 rounded-2xl bg-[#f8faf9] p-4">
                        <dt class="text-slate-500">Status</dt>
                        <dd class="font-black text-teal-800">{{ $order->statusLabel() }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 rounded-2xl bg-[#f8faf9] p-4">
                        <dt class="text-slate-500">Payment</dt>
                        <dd class="font-black text-slate-950">{{ strtoupper($order->payment_status) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 rounded-2xl bg-[#f8faf9] p-4">
                        <dt class="text-slate-500">Tracking</dt>
                        <dd class="font-black text-slate-950">{{ $order->tracking_code ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 rounded-2xl bg-[#f8faf9] p-4">
                        <dt class="text-slate-500">Shipment</dt>
                        <dd class="text-right font-black text-slate-950">{{ $order->shipping_provider ?: 'Agape153' }}<br><span class="text-teal-800">{{ $order->shippingStatusLabel() }}</span></dd>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Subtotal</dt>
                            <dd class="font-black text-slate-950">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</dd>
                        </div>
                        <div class="mt-3 flex justify-between gap-4">
                            <dt class="text-slate-500">Shipping</dt>
                            <dd class="font-black text-slate-950">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</dd>
                        </div>
                    </div>
                    <div class="flex justify-between gap-4 rounded-2xl bg-teal-50 p-4 text-lg">
                        <dt class="font-black text-slate-950">Total</dt>
                        <dd class="font-black text-teal-800">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                @if ($order->wa_checkout_url)
                    <a class="btn-primary mt-6 w-full" href="{{ $order->wa_checkout_url }}" target="_blank" rel="noopener">
                        <x-icon name="phone" class="h-4 w-4" />
                        Continue WhatsApp
                    </a>
                @endif
                @if ($order->tracking_url)
                    <a class="btn-secondary mt-3 w-full" href="{{ $order->tracking_url }}" target="_blank" rel="noopener">
                        <x-icon name="truck" class="h-4 w-4" />
                        Open Logistic Tracking
                    </a>
                @endif
            </aside>
        </div>
    </section>
@endsection
