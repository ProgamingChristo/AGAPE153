@extends('layouts.admin')

@section('title', ($product->exists ? 'Edit Product' : 'Add Product').' - Admin Agape153')

@section('content')
    <div>
        <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Products</p>
        <h1 class="mt-3 text-4xl font-black text-slate-950">{{ $product->exists ? 'Edit product' : 'Add product' }}.</h1>
    </div>

    <form class="mt-8 grid gap-6 rounded-2xl border border-slate-200 bg-white p-6" method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if ($product->exists)
            @method('PUT')
        @endif
        <div class="grid gap-4 md:grid-cols-2">
            <label class="grid gap-2 text-sm font-bold">Category
                <select class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="grid gap-2 text-sm font-bold">Name
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="name" value="{{ old('name', $product->name) }}" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">Slug
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="slug" value="{{ old('slug', $product->slug) }}">
            </label>
            <label class="grid gap-2 text-sm font-bold">SKU
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="sku" value="{{ old('sku', $product->sku) }}">
            </label>
            <label class="grid gap-2 text-sm font-bold">Origin
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="origin" value="{{ old('origin', $product->origin) }}">
            </label>
            <label class="grid gap-2 text-sm font-bold">Grade
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="grade" value="{{ old('grade', $product->grade) }}">
            </label>
            <label class="grid gap-2 text-sm font-bold">Unit
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="unit" value="{{ old('unit', $product->unit ?: 'Kg') }}" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">Price
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" type="number" name="price" value="{{ old('price', $product->price) }}" min="0" step="100">
            </label>
            <label class="grid gap-2 text-sm font-bold">Currency
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="currency" value="{{ old('currency', $product->currency ?: 'IDR') }}" maxlength="3" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">Stock
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" min="0" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">MOQ
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" type="number" name="min_order_quantity" value="{{ old('min_order_quantity', $product->min_order_quantity ?: 1) }}" min="1" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">Image URL
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="image_url" value="{{ old('image_url', $product->image_url) }}">
            </label>
        </div>
        <label class="grid gap-2 text-sm font-bold">Short Description
            <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3 font-normal" name="short_description">{{ old('short_description', $product->short_description) }}</textarea>
        </label>
        <label class="grid gap-2 text-sm font-bold">Description
            <textarea class="min-h-40 rounded-xl border border-slate-200 px-4 py-3 font-normal" name="description">{{ old('description', $product->description) }}</textarea>
        </label>
        <div class="grid gap-4 md:grid-cols-3">
            <label class="flex items-center gap-2 text-sm font-bold">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
                Active
            </label>
            <label class="flex items-center gap-2 text-sm font-bold">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))>
                Featured
            </label>
            <label class="flex items-center gap-2 text-sm font-bold">
                <input type="checkbox" name="export_ready" value="1" @checked(old('export_ready', $product->export_ready))>
                Export ready
            </label>
        </div>
        <div class="flex gap-3">
            <button class="btn-primary" type="submit">Save Product</button>
            <a class="btn-secondary" href="{{ route('admin.products.index') }}">Cancel</a>
        </div>
    </form>
@endsection
