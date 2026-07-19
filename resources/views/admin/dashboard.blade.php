@extends('layouts.admin')

@section('title', 'Admin Dashboard - Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Dashboard</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Business overview.</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="btn-secondary" href="{{ route('admin.orders.index') }}"><x-icon name="orders" class="h-4 w-4" />Manage Orders</a>
            <a class="btn-primary" href="{{ route('admin.products.create') }}">Add Product</a>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ([['Products', $totalProducts, 'package'], ['Orders', $totalOrders, 'orders'], ['Members', $totalMembers, 'user'], ['Messages', $newMessages, 'message'], ['Product Views', $totalViews, 'search']] as [$label, $value, $icon])
            <div class="lift-card rounded-2xl border border-slate-200 bg-white p-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="text-sm font-bold text-slate-500">{{ $label }}</div>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-teal-50 text-teal-700">
                        <x-icon :name="$icon" class="h-5 w-5" />
                    </span>
                </div>
                <div class="mt-4 text-3xl font-black text-slate-950">{{ number_format($value) }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Recent Orders</h2>
            <div class="mt-5 grid gap-3">
                @forelse ($recentOrders as $order)
                    <div class="rounded-xl bg-[#f8faf9] p-4">
                        <div class="flex justify-between gap-4">
                            <strong>{{ $order->order_number }}</strong>
                            <span class="text-sm font-bold text-teal-800">{{ $order->statusLabel() }}</span>
                        </div>
                        <div class="mt-1 text-sm text-slate-600">{{ $order->customer_name }} / Rp{{ number_format($order->total_amount, 0, ',', '.') }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">Belum ada order.</p>
                @endforelse
            </div>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Top Product Views</h2>
            <div class="mt-5 grid gap-3">
                @forelse ($topProducts as $product)
                    <a class="flex justify-between gap-4 rounded-xl bg-[#f8faf9] p-4" href="{{ route('admin.products.edit', $product) }}">
                        <span class="font-bold">{{ $product->name }}</span>
                        <span class="font-black text-teal-800">{{ number_format($product->view_count) }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-600">Belum ada produk.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
