@extends('layouts.app')

@section('title', 'Product Catalog - Agape153')
@section('description', 'Katalog rempah-rempah dan kopi Indonesia Agape153.')

@section('content')
    <section class="bg-white py-12">
        <div class="agape-container">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Product Catalog</p>
                    <h1 class="mt-3 text-4xl font-black text-slate-950">Rempah-rempah dan kopi Indonesia.</h1>
                </div>
                <a href="{{ route('cart.index') }}" class="btn-secondary">View Cart</a>
            </div>

            <form class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-[#f8faf9] p-4 md:grid-cols-[1fr_220px_160px_120px]" method="GET">
                <input class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-teal-500" type="search" name="q" value="{{ $query }}" placeholder="Search product, origin, grade">
                <select class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-teal-500" name="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700">
                    <input type="checkbox" name="export_ready" value="1" @checked(request()->boolean('export_ready'))>
                    Export
                </label>
                <button class="btn-primary" type="submit">Filter</button>
            </form>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($products as $product)
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <a href="{{ route('products.show', $product) }}">
                            <img class="h-56 w-full object-cover" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        </a>
                        <div class="p-6">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-black uppercase tracking-[0.16em] text-teal-700">
                                <span>{{ $product->category->name }}</span>
                                @if ($product->export_ready)
                                    <span class="rounded-full bg-amber-100 px-2 py-1 text-amber-800">Export</span>
                                @endif
                            </div>
                            <h2 class="mt-3 text-xl font-black text-slate-950">{{ $product->name }}</h2>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $product->short_description }}</p>
                            <div class="mt-5 flex items-center justify-between gap-4">
                                <span class="font-black text-teal-800">{{ $product->formattedPrice() }}</span>
                                <a href="{{ route('products.show', $product) }}" class="text-sm font-black text-slate-950 hover:text-teal-700">Detail</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-slate-600 sm:col-span-2 lg:col-span-3">Belum ada produk yang cocok dengan filter.</div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </section>
@endsection
