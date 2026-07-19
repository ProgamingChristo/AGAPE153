@extends('layouts.admin')

@section('title', $order->order_number.' - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Order Detail</p>
            <h1 class="mt-3 break-words text-4xl font-black text-slate-950">{{ $order->order_number }}</h1>
            <p class="mt-2 text-sm font-semibold text-slate-500">
                {{ $order->created_at->format('d M Y H:i') }}
                @if ($order->accepted_at)
                    / accepted by {{ $order->acceptedBy?->name ?? 'Admin' }} at {{ $order->accepted_at->format('d M Y H:i') }}
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if ($order->canBeAccepted())
                <form method="POST" action="{{ route('admin.orders.accept', $order) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn-primary" type="submit"><x-icon name="check" class="h-4 w-4" />ACC Order</button>
                </form>
            @endif
            <a class="btn-secondary" href="{{ route('admin.orders.shipping-label', $order) }}" target="_blank" rel="noopener"><x-icon name="print" class="h-4 w-4" />Cetak Resi</a>
            <a class="btn-secondary" href="{{ route('admin.orders.index') }}"><x-icon name="orders" class="h-4 w-4" />Back</a>
        </div>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-[1fr_380px]">
        <div class="grid gap-6">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 bg-[#f8faf9] p-5">
                    <h2 class="text-xl font-black text-slate-950">Ordered products</h2>
                </div>
                @foreach ($order->items as $item)
                    <div class="grid gap-4 border-b border-slate-100 p-5 md:grid-cols-[72px_1fr_120px_130px] md:items-center">
                        <img class="h-20 w-20 rounded-xl object-cover" src="{{ $item->product_image_url ?: $item->product?->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $item->product_name }}">
                        <div>
                            <div class="font-black text-slate-950">{{ $item->product_name }}</div>
                            <div class="mt-1 text-sm text-slate-500">{{ $item->product_sku ?: 'SKU unavailable' }}</div>
                        </div>
                        <div class="text-sm font-bold text-slate-700">{{ $item->quantity }} {{ $item->unit }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</div>
                        <div class="font-black text-slate-950">Rp{{ number_format($item->line_total, 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-start">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.18em] text-teal-700">Shipment timeline</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">{{ $order->shippingStatusLabel() }}</h2>
                    </div>
                    <span class="status-pill border-teal-200 bg-teal-50 text-teal-800">{{ $order->statusLabel() }}</span>
                </div>
                <div class="mt-6 grid gap-4">
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
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-teal-700">Product reviews</p>
                <h2 class="mt-2 text-2xl font-black text-slate-950">Customer rating and comments.</h2>
                <div class="mt-5 grid gap-4">
                    @foreach ($order->items as $item)
                        <div class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-4">
                            <div class="font-black text-slate-950">{{ $item->product_name }}</div>
                            @if ($item->review)
                                <div class="mt-3 flex items-center gap-2 text-amber-500">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <x-icon name="star" class="h-4 w-4 {{ $i <= $item->review->rating ? 'fill-current' : 'text-slate-300' }}" />
                                    @endfor
                                    <span class="text-sm font-black text-slate-950">{{ $item->review->rating }}/5</span>
                                </div>
                                <p class="mt-3 rounded-xl bg-white p-4 text-sm leading-6 text-slate-600">{{ $item->review->comment ?: 'No comment.' }}</p>
                                @if ($item->review->admin_reply)
                                    <div class="mt-3 rounded-xl border border-teal-200 bg-teal-50 p-4 text-sm leading-6 text-teal-900">
                                        <div class="font-black">Admin reply</div>
                                        <p class="mt-1">{{ $item->review->admin_reply }}</p>
                                    </div>
                                @endif
                                <form class="mt-3 grid gap-3" method="POST" action="{{ route('admin.reviews.reply', $item->review) }}">
                                    @csrf
                                    @method('PATCH')
                                    <textarea class="min-h-24 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-teal-500" name="admin_reply" placeholder="Balas komentar customer..." required>{{ old('admin_reply', $item->review->admin_reply) }}</textarea>
                                    <button class="btn-primary w-full px-4 py-2 text-sm sm:w-max" type="submit">Save Reply</button>
                                </form>
                            @else
                                <div class="mt-3 rounded-xl bg-white p-4 text-sm font-semibold text-slate-500">Customer belum memberi rating produk ini.</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="grid h-max gap-6">
            <form class="rounded-2xl border border-teal-200 bg-teal-50 p-6" method="POST" action="{{ route('admin.orders.shipment.update', $order) }}">
                @csrf
                @method('PATCH')
                <h2 class="text-xl font-black text-slate-950">Proses resi pengiriman</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Isi kurir, nomor resi, dan status paket. Timeline customer akan ikut berubah.</p>
                <div class="mt-5 grid gap-4">
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Shipping Provider
                        <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="shipping_provider" value="{{ old('shipping_provider', $order->shipping_provider) }}" placeholder="JNE, DHL, FedEx, etc.">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Nomor Resi
                        <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="tracking_code" value="{{ old('tracking_code', $order->tracking_code) }}" placeholder="Input resi pengiriman">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Status Pengiriman
                        <select class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="shipping_status" required>
                            @foreach ($shippingStatuses as $value => $label)
                                <option value="{{ $value }}" @selected(old('shipping_status', $order->shipping_status ?: 'order_created') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Tracking URL
                        <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="tracking_url" value="{{ old('tracking_url', $order->tracking_url) }}" placeholder="https://...">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Shipping Notes
                        <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3 font-normal" name="shipping_notes" placeholder="Catatan packing, pickup, atau ekspedisi">{{ old('shipping_notes', $order->shipping_notes) }}</textarea>
                    </label>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <button class="btn-primary" type="submit"><x-icon name="truck" class="h-4 w-4" />Update Resi</button>
                        <a class="btn-secondary" href="{{ route('admin.orders.shipping-label', $order) }}" target="_blank" rel="noopener"><x-icon name="print" class="h-4 w-4" />Cetak</a>
                    </div>
                </div>
            </form>

            <form class="rounded-2xl border border-slate-200 bg-white p-6" method="POST" action="{{ route('admin.orders.update', $order) }}">
                @csrf
                @method('PUT')
                <h2 class="text-xl font-black text-slate-950">Order control</h2>
                <div class="mt-5 grid gap-4">
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Status
                        <select class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="status" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Payment
                        <select class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="payment_status" required>
                            @foreach ($paymentStatuses as $status)
                                <option value="{{ $status }}" @selected(old('payment_status', $order->payment_status) === $status)>{{ strtoupper($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Shipping Cost
                        <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" type="number" min="0" step="100" name="shipping_cost" value="{{ old('shipping_cost', (int) $order->shipping_cost) }}">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Discount Amount
                        <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" type="number" min="0" step="100" name="discount_amount" value="{{ old('discount_amount', (int) $order->discount_amount) }}">
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Quotation Status
                        <select class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="quotation_status">
                            @foreach (['draft', 'sent', 'accepted', 'rejected'] as $status)
                                <option value="{{ $status }}" @selected(old('quotation_status', $order->quotation_status ?: 'draft') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Quotation Notes
                        <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3 font-normal" name="quotation_notes">{{ old('quotation_notes', $order->quotation_notes) }}</textarea>
                    </label>
                    <label class="grid gap-2 text-sm font-bold text-slate-700">Admin Notes
                        <textarea class="min-h-28 rounded-xl border border-slate-200 px-4 py-3 font-normal" name="notes">{{ old('notes', $order->notes) }}</textarea>
                    </label>
                    <button class="btn-primary" type="submit"><x-icon name="check" class="h-4 w-4" />Save Order</button>
                </div>
            </form>

            <section class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-6">
                <h2 class="text-xl font-black text-slate-950">Customer</h2>
                <dl class="mt-5 grid gap-4 text-sm">
                    <div>
                        <dt class="text-slate-500">Name</dt>
                        <dd class="font-black text-slate-950">{{ $order->customer_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Email</dt>
                        <dd class="font-black text-slate-950">{{ $order->customer_email ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Phone</dt>
                        <dd class="font-black text-slate-950">{{ $order->customer_phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Shipping</dt>
                        <dd class="font-black text-slate-950">{{ $order->shipping_address }}, {{ $order->country }}</dd>
                    </div>
                    <div class="border-t border-slate-200 pt-4">
                        <dt class="text-slate-500">Total</dt>
                        <dd class="text-2xl font-black text-teal-800">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
@endsection
