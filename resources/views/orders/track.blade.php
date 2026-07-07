@extends('layouts.app')

@section('title', 'Order Tracking - Agape153')

@section('content')
    <section class="bg-white py-12">
        <div class="agape-container max-w-3xl">
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Order Tracking</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Cek status order.</h1>
            <form class="mt-8 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-[#f8faf9] p-4 sm:flex-row" method="POST" action="{{ route('orders.track.search') }}">
                @csrf
                <input class="w-full rounded-xl border border-slate-200 px-4 py-3" name="keyword" value="{{ old('keyword') }}" placeholder="Order number or tracking code" required>
                <button class="btn-primary" type="submit">Search</button>
            </form>

            @isset($order)
                @if ($order)
                    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row">
                            <div>
                                <div class="text-sm text-slate-500">Order Number</div>
                                <div class="text-2xl font-black text-slate-950">{{ $order->order_number }}</div>
                            </div>
                            <div class="rounded-full bg-teal-100 px-4 py-2 text-sm font-black text-teal-800">{{ $order->statusLabel() }}</div>
                        </div>
                        <div class="mt-6 grid gap-3">
                            @foreach ($order->items as $item)
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-3 text-sm">
                                    <span>{{ $item->product_name }} x {{ $item->quantity }}</span>
                                    <strong>Rp{{ number_format($item->line_total, 0, ',', '.') }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-6 font-bold text-amber-800">Order tidak ditemukan.</div>
                @endif
            @endisset
        </div>
    </section>
@endsection
