@extends('layouts.app')

@section('title', 'Member Dashboard - Agape153')

@section('content')
    <section class="bg-white py-12">
        <div class="agape-container">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Member Area</p>
                    <h1 class="mt-3 text-4xl font-black text-slate-950">Halo, {{ auth()->user()->name }}.</h1>
                </div>
                <div class="flex gap-3">
                    <a class="btn-secondary" href="{{ route('member.profile') }}">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-primary" type="submit">Logout</button>
                    </form>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-6">
                    <h2 class="text-xl font-black text-slate-950">Purchase History</h2>
                    <div class="mt-5 grid gap-3">
                        @forelse ($orders as $order)
                            <a class="rounded-xl bg-[#f8faf9] p-4" href="{{ route('orders.track', ['keyword' => $order->order_number]) }}">
                                <div class="flex justify-between gap-4">
                                    <strong>{{ $order->order_number }}</strong>
                                    <span class="text-sm font-bold text-teal-800">{{ $order->statusLabel() }}</span>
                                </div>
                                <div class="mt-1 text-sm text-slate-600">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            </a>
                        @empty
                            <p class="text-sm text-slate-600">Belum ada order.</p>
                        @endforelse
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200 p-6">
                    <h2 class="text-xl font-black text-slate-950">Favorite Product</h2>
                    <div class="mt-5 grid gap-3">
                        @forelse ($wishlists as $wishlist)
                            @if ($wishlist->product)
                                <a class="flex gap-3 rounded-xl bg-[#f8faf9] p-3" href="{{ route('products.show', $wishlist->product) }}">
                                    <img class="h-16 w-16 rounded-lg object-cover" src="{{ $wishlist->product->image_url }}" alt="{{ $wishlist->product->name }}">
                                    <div>
                                        <div class="font-black text-slate-950">{{ $wishlist->product->name }}</div>
                                        <div class="text-sm text-slate-600">{{ $wishlist->product->formattedPrice() }}</div>
                                    </div>
                                </a>
                            @endif
                        @empty
                            <p class="text-sm text-slate-600">Belum ada wishlist.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
