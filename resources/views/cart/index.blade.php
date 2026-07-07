@extends('layouts.app')

@section('title', 'Cart - Agape153')

@section('content')
    <section class="bg-white py-12">
        <div class="agape-container">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Cart</p>
                    <h1 class="mt-3 text-4xl font-black text-slate-950">Review order items.</h1>
                </div>
                <a class="btn-secondary" href="{{ route('products.index') }}">Continue Shopping</a>
            </div>

            @if ($lines->isEmpty())
                <div class="mt-8 rounded-2xl border border-slate-200 bg-[#f8faf9] p-8 text-slate-600">Cart masih kosong.</div>
            @else
                <form class="mt-8" action="{{ route('cart.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        @foreach ($lines as $line)
                            <div class="grid gap-4 border-b border-slate-100 p-4 md:grid-cols-[96px_1fr_160px_140px] md:items-center">
                                <img class="h-24 w-24 rounded-xl object-cover" src="{{ $line['product']->image_url }}" alt="{{ $line['product']->name }}">
                                <div>
                                    <a class="font-black text-slate-950 hover:text-teal-700" href="{{ route('products.show', $line['product']) }}">{{ $line['product']->name }}</a>
                                    <div class="mt-1 text-sm text-slate-600">{{ $line['product']->formattedPrice() }}</div>
                                </div>
                                <input class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-teal-500" type="number" name="quantities[{{ $line['product']->id }}]" value="{{ $line['quantity'] }}" min="0">
                                <div class="font-black text-slate-950">Rp{{ number_format($line['line_total'], 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex flex-col gap-4 rounded-2xl bg-[#f8faf9] p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-sm text-slate-600">Subtotal</div>
                            <div class="text-2xl font-black text-teal-800">Rp{{ number_format($subtotal, 0, ',', '.') }}</div>
                        </div>
                        <div class="flex gap-3">
                            <button class="btn-secondary" type="submit">Update Cart</button>
                            <a class="btn-primary" href="{{ route('checkout.create') }}">Checkout</a>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </section>
@endsection
