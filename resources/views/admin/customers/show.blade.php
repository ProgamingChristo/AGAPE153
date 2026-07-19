@extends('layouts.admin')

@section('title', $customer->name.' - Customer')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Customer</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">{{ $customer->name }}</h1>
            <p class="mt-2 text-sm font-semibold text-slate-500">{{ $customer->email }} / {{ $customer->phone ?: '-' }}</p>
        </div>
        <a class="btn-secondary" href="{{ route('admin.customers.index') }}">Back</a>
    </div>

    <div class="mt-8 grid gap-6 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-sm font-bold text-slate-500">Orders</div>
            <div class="mt-2 text-3xl font-black text-slate-950">{{ $customer->orders->count() }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-sm font-bold text-slate-500">Total Spend</div>
            <div class="mt-2 text-3xl font-black text-teal-800">Rp{{ number_format($customer->orders->sum('total_amount'), 0, ',', '.') }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-sm font-bold text-slate-500">Lifecycle</div>
            <div class="mt-2 text-3xl font-black text-slate-950">{{ $customer->orders->count() > 1 ? 'Repeat' : 'New' }}</div>
        </div>
    </div>

    <section class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($customer->orders->sortByDesc('created_at') as $order)
            <a class="grid gap-3 border-b border-slate-100 p-5 md:grid-cols-[1fr_140px_160px]" href="{{ route('admin.orders.show', $order) }}">
                <div>
                    <div class="font-black text-slate-950">{{ $order->order_number }}</div>
                    <div class="mt-1 text-sm text-slate-500">{{ $order->created_at->format('d M Y') }} / {{ $order->items->count() }} item</div>
                </div>
                <div class="font-black text-slate-700">{{ $order->statusLabel() }}</div>
                <div class="font-black text-teal-800">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</div>
            </a>
        @empty
            <div class="p-6 text-slate-600">No orders yet.</div>
        @endforelse
    </section>
@endsection
