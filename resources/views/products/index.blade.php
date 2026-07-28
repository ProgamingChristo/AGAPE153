@extends('layouts.app')

@section('title', 'Product Catalog - Agape153')
@section('description', 'Katalog rempah-rempah, kopi, dan komoditas pertanian Indonesia Agape153.')

@section('content')
    @php
        $t = $siteText ?? [];
        $quoteLabel = $t['product.quote_label'] ?? 'Please contact or drop us email';
        $quoteHint = $t['product.quote_hint'] ?? 'Pricing depends on MOQ';
    @endphp
    <section class="bg-[#101820] text-white">
        <div class="logo-stripe"><span></span><span></span><span></span></div>
        <div class="agape-container grid gap-6 py-10 lg:grid-cols-[1fr_360px] lg:items-end">
            <div data-reveal>
                <p class="section-kicker text-amber-200"><x-icon name="package" class="h-4 w-4" />Product Catalog</p>
                <h1 class="mt-3 max-w-4xl text-5xl font-black leading-tight sm:text-7xl">Trade list for Indonesian commodities.</h1>
                <p class="mt-4 max-w-2xl leading-8 text-slate-200">Clear product information for faster sourcing decisions: category, origin, minimum order, stock, shipping readiness, and quote guidance.</p>
            </div>
            <aside class="border border-white/10 bg-white/10 p-5" data-reveal>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-slate-300">Listed Products</span>
                    <strong class="text-4xl font-black text-amber-200">{{ $products->total() }}</strong>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <span class="text-sm font-bold text-slate-300">Categories</span>
                    <strong class="text-4xl font-black text-sky-200">{{ $categories->count() }}</strong>
                </div>
                <a href="{{ route('cart.index') }}" class="btn-secondary mt-5 w-full">
                    <x-icon name="cart" class="h-4 w-4" />
                    View Cart
                </a>
            </aside>
        </div>
    </section>

    <section class="border-b border-slate-200 bg-white py-4">
        <div class="agape-container">
            <form class="grid gap-3 md:grid-cols-[1fr_240px_150px_130px]" method="GET" data-reveal>
                <input class="field-input" type="search" name="q" value="{{ $query }}" placeholder="Search product, origin, grade">
                <select class="field-input" name="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <label class="flex items-center gap-2 border border-slate-200 bg-[#f8faf9] px-4 py-3 text-sm font-black text-slate-700">
                    <input type="checkbox" name="export_ready" value="1" @checked(request()->boolean('export_ready'))>
                    {{ $t['home.shipping_ready'] ?? 'Shipping Ready' }}
                </label>
                <button class="btn-primary" type="submit">
                    <x-icon name="search" class="h-4 w-4" />
                    Filter
                </button>
            </form>
        </div>
    </section>

    <section class="section-pad bg-[#f4f6f3]">
        <div class="agape-container">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center" data-reveal>
                <div class="trade-ticker text-slate-950">
                    <span class="border-slate-200 bg-white">Spices</span>
                    <span class="border-slate-200 bg-white">Coffee</span>
                    <span class="border-slate-200 bg-white">Agriculture</span>
                    <span class="border-slate-200 bg-white">{{ $t['nav.shipping'] ?? 'Shipping' }}</span>
                </div>
                @if ($query || $selectedCategory || request()->boolean('export_ready'))
                    <a class="btn-secondary" href="{{ route('products.index') }}">Clear Filter</a>
                @endif
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3" data-reveal>
                @forelse ($products as $product)
                    <a class="group overflow-hidden rounded-[1.35rem] border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-teal-200 hover:shadow-[0_24px_60px_rgba(15,23,42,0.12)]" href="{{ route('products.show', $product) }}">
                        <div class="relative overflow-hidden bg-slate-100">
                            <img class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105 sm:aspect-[4/3]" src="{{ $product->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $product->name }}" loading="lazy" decoding="async">
                            <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                                <span class="rounded-full bg-white/90 px-3 py-1 text-xs font-black text-teal-800 shadow-sm backdrop-blur">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                                <span class="rounded-full bg-white/90 px-3 py-1 text-xs font-black text-slate-700 shadow-sm backdrop-blur">{{ $product->stock_quantity > 0 ? 'In Stock' : 'Preorder' }}</span>
                            </div>
                            @if ($product->export_ready || $product->video_url)
                                <div class="absolute bottom-3 left-3 right-3 flex flex-wrap gap-2">
                                    @if ($product->export_ready)
                                        <span class="rounded-full bg-amber-200/95 px-3 py-1 text-xs font-black text-slate-950 shadow-sm backdrop-blur">{{ $t['home.shipping_ready'] ?? 'Shipping Ready' }}</span>
                                    @endif
                                    @if ($product->video_url)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-sky-100/95 px-3 py-1 text-xs font-black text-sky-900 shadow-sm backdrop-blur">
                                            <x-icon name="video" class="h-3.5 w-3.5" />
                                            Video
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="grid min-h-72 gap-4 p-5">
                            <div>
                                <h2 class="line-clamp-2 break-words text-xl font-black leading-snug text-slate-950">{{ $product->name }}</h2>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $product->short_description }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-3">
                                    <div class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">MOQ</div>
                                    <div class="mt-1 font-black text-slate-950">{{ $product->min_order_quantity }} {{ $product->unit }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-3">
                                    <div class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">Stock</div>
                                    <div class="mt-1 font-black text-slate-950">{{ $product->stock_quantity }} {{ $product->unit }}</div>
                                </div>
                            </div>

                            <div class="mt-auto flex items-end justify-between gap-3 border-t border-slate-100 pt-4">
                                <div>
                                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">Quote</div>
                                    <div class="mt-1 text-base font-black leading-snug text-teal-800">{{ $quoteLabel }}</div>
                                    <p class="mt-1 text-xs font-bold text-slate-500">{{ $quoteHint }}</p>
                                </div>
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#101820] text-white transition group-hover:bg-teal-700">
                                    <x-icon name="arrow" class="h-4 w-4" />
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-[1.35rem] border border-slate-200 bg-white p-8 text-slate-600 sm:col-span-2 xl:col-span-3">
                        <div class="text-xl font-black text-slate-950">No matching products yet.</div>
                        <p class="mt-2 text-sm leading-6">Try another keyword or contact Agape153 for custom sourcing requests.</p>
                        <a class="btn-primary mt-5" href="{{ route('home') }}#contact">
                            <x-icon name="message" class="h-4 w-4" />
                            Send Inquiry
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </section>
@endsection
