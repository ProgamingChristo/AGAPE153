@extends('layouts.admin')

@section('title', 'Products - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Products</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Manage catalog.</h1>
        </div>
        <a class="btn-primary" href="{{ route('admin.products.create') }}">Add Product</a>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="grid border-b border-slate-100 bg-slate-50 px-4 py-3 text-sm font-black text-slate-600 md:grid-cols-[1fr_160px_140px_120px]">
            <span>Product</span>
            <span>Category</span>
            <span>Price</span>
            <span>Actions</span>
        </div>
        @foreach ($products as $product)
            <div class="grid gap-3 border-b border-slate-100 px-4 py-4 text-sm md:grid-cols-[1fr_160px_140px_120px] md:items-center">
                <div class="flex items-center gap-3">
                    <img class="h-14 w-14 rounded-lg object-cover" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                    <div>
                        <div class="font-black text-slate-950">{{ $product->name }}</div>
                        <div class="text-slate-500">{{ $product->sku }}</div>
                    </div>
                </div>
                <div>{{ $product->category?->name }}</div>
                <div>{{ $product->formattedPrice() }}</div>
                <div class="flex gap-2">
                    <a class="font-black text-teal-700" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button class="font-black text-red-700" type="submit">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $products->links() }}</div>
@endsection
