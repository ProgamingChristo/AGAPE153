@extends('layouts.app')

@section('title', 'Cart - Agape153')

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30" src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1800&q=80" alt="Coffee and spices order cart">
            <div class="absolute inset-0 bg-slate-950/72"></div>
        </div>
        <div class="agape-container relative py-12" data-reveal>
            <p class="section-kicker text-amber-200"><x-icon name="cart" class="h-4 w-4" />Cart</p>
            <div class="mt-3 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <h1 class="text-4xl font-black text-white sm:text-6xl">Review order items.</h1>
                    <p class="mt-4 max-w-2xl leading-8 text-slate-200">Check quantity, subtotal, and continue to Online or WhatsApp checkout.</p>
                </div>
                <a class="btn-secondary text-slate-950" href="{{ route('products.index') }}">
                    <x-icon name="package" class="h-4 w-4" />
                    Continue Shopping
                </a>
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#f8faf9]">
        <div class="agape-container">
            @if ($lines->isEmpty())
                <div class="grid gap-8 rounded-3xl border border-slate-200 bg-white p-8 md:grid-cols-[1fr_300px] md:items-center" data-reveal>
                    <div>
                        <x-logo />
                        <h2 class="mt-6 text-3xl font-black text-slate-950">Cart masih kosong.</h2>
                        <p class="mt-3 max-w-xl leading-7 text-slate-600">Pilih produk dari katalog, lalu login untuk menambahkan item ke keranjang dan melanjutkan checkout.</p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a class="btn-primary" href="{{ route('products.index') }}">
                                <x-icon name="package" class="h-4 w-4" />
                                Browse Products
                            </a>
                            @guest
                                <a class="btn-secondary" href="{{ route('login') }}">
                                    <x-icon name="login" class="h-4 w-4" />
                                    Login
                                </a>
                            @endguest
                        </div>
                    </div>
                    <div class="rounded-3xl bg-[#edf7f4] p-6">
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <span class="brand-tile bg-[#e64b3c] text-3xl text-slate-950">1</span>
                            <span class="brand-tile bg-[#f2d763] text-3xl text-slate-950">5</span>
                            <span class="brand-tile bg-[#63c6dc] text-3xl text-slate-950">3</span>
                        </div>
                    </div>
                </div>
            @else
                <form class="grid gap-6 lg:grid-cols-[1fr_360px]" action="{{ route('cart.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" data-reveal>
                        @foreach ($lines as $line)
                            <div class="grid gap-4 border-b border-slate-100 p-5 md:grid-cols-[96px_1fr_150px_140px] md:items-center">
                                <img class="h-24 w-24 rounded-2xl object-cover" src="{{ $line['product']->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $line['product']->name }}">
                                <div>
                                    <a class="text-lg font-black text-slate-950 hover:text-teal-700" href="{{ route('products.show', $line['product']) }}">{{ $line['product']->name }}</a>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <span class="status-pill">{{ $line['product']->formattedPrice() }}</span>
                                        <span class="status-pill">{{ $line['product']->stock_quantity }} {{ $line['product']->unit }} stock</span>
                                    </div>
                                </div>
                                <input class="field-input" type="number" name="quantities[{{ $line['product']->id }}]" value="{{ $line['quantity'] }}" min="0">
                                <div class="text-lg font-black text-slate-950">Rp{{ number_format($line['line_total'], 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>

                    <aside class="h-max rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" data-reveal>
                        <p class="section-kicker"><x-icon name="orders" class="h-4 w-4" />Order Summary</p>
                        <div class="mt-5 grid gap-3 text-sm">
                            <div class="flex justify-between gap-4 rounded-2xl bg-[#f8faf9] p-4">
                                <span class="text-slate-600">Items</span>
                                <strong class="text-slate-950">{{ $lines->sum('quantity') }}</strong>
                            </div>
                            <div class="flex justify-between gap-4 rounded-2xl bg-[#f8faf9] p-4">
                                <span class="text-slate-600">Subtotal</span>
                                <strong class="text-teal-800">Rp{{ number_format($subtotal, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="mt-6 grid gap-3">
                            <button class="btn-secondary w-full" type="submit">
                                <x-icon name="history" class="h-4 w-4" />
                                Update Cart
                            </button>
                            <a class="btn-primary w-full" href="{{ route('checkout.create') }}">
                                <x-icon name="lock" class="h-4 w-4" />
                                Checkout
                            </a>
                        </div>
                        <p class="mt-4 text-xs leading-5 text-slate-500">Set quantity to 0 and update cart to remove an item.</p>
                    </aside>
                </form>
            @endif
        </div>
    </section>
@endsection
