@extends('layouts.admin')

@section('title', 'Orders - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Orders</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Approve and manage orders.</h1>
        </div>
    </div>

    <form class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_180px_180px_auto]" method="GET">
        <input class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-teal-500" name="q" value="{{ request('q') }}" placeholder="Search order, customer, tracking">
        <select class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-teal-500" name="status">
            <option value="">All status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <select class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-teal-500" name="payment_status">
            <option value="">All payment</option>
            @foreach ($paymentStatuses as $status)
                <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ strtoupper($status) }}</option>
            @endforeach
        </select>
        <button class="btn-primary" type="submit"><x-icon name="search" class="h-4 w-4" />Filter</button>
    </form>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($orders as $order)
            <div class="grid gap-4 border-b border-slate-100 p-5 xl:grid-cols-[1.2fr_1fr_150px_150px_140px_220px] xl:items-center">
                <div>
                    <a class="break-words text-lg font-black text-slate-950 hover:text-teal-700" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
                    <div class="mt-1 text-sm font-semibold text-slate-500">{{ $order->created_at->format('d M Y H:i') }} / {{ $order->items_count }} item</div>
                </div>
                <div>
                    <div class="font-bold text-slate-900">{{ $order->customer_name }}</div>
                    <div class="mt-1 text-sm text-slate-500">{{ $order->customer_phone }}</div>
                </div>
                <div>
                    <span class="inline-flex rounded-full bg-teal-100 px-3 py-1 text-sm font-black text-teal-800">{{ $order->statusLabel() }}</span>
                    <div class="mt-2 text-xs font-black uppercase text-slate-500">{{ $order->payment_status }}</div>
                    @if ($order->approval_status === 'needs_review')
                        <div class="mt-2 text-xs font-black uppercase text-amber-700">Needs review</div>
                    @endif
                </div>
                <div>
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Shipment</div>
                    <div class="mt-1 font-black text-slate-950">{{ $order->shippingStatusLabel() }}</div>
                    <div class="mt-1 text-xs font-bold text-slate-500">{{ $order->tracking_code ?: 'No resi' }}</div>
                </div>
                <div class="font-black text-slate-950">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</div>
                <div class="flex flex-wrap gap-2 xl:justify-end">
                    @if ($order->canBeAccepted())
                        <form method="POST" action="{{ route('admin.orders.accept', $order) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn-primary px-3 py-2 text-sm" type="submit">
                                <x-icon name="check" class="h-4 w-4" />
                                ACC
                            </button>
                        </form>
                    @endif
                    <a class="btn-secondary px-3 py-2 text-sm" href="{{ route('admin.orders.show', $order) }}">Detail</a>
                </div>
            </div>
        @empty
            <div class="p-8 text-slate-600">Belum ada order.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
