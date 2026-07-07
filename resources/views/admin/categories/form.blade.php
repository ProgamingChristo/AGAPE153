@extends('layouts.admin')

@section('title', ($category->exists ? 'Edit Category' : 'Add Category').' - Admin Agape153')

@section('content')
    <div>
        <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Categories</p>
        <h1 class="mt-3 text-4xl font-black text-slate-950">{{ $category->exists ? 'Edit category' : 'Add category' }}.</h1>
    </div>

    <form class="mt-8 grid gap-6 rounded-2xl border border-slate-200 bg-white p-6" method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if ($category->exists)
            @method('PUT')
        @endif
        <div class="grid gap-4 md:grid-cols-2">
            <label class="grid gap-2 text-sm font-bold">Parent
                <select class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="parent_id">
                    <option value="">Root category</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="grid gap-2 text-sm font-bold">Type
                <select class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="type" required>
                    @foreach (['spice' => 'Spice', 'coffee' => 'Coffee', 'export' => 'Export', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $category->type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="grid gap-2 text-sm font-bold">Name
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="name" value="{{ old('name', $category->name) }}" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">Slug
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="slug" value="{{ old('slug', $category->slug) }}">
            </label>
            <label class="grid gap-2 text-sm font-bold">Image URL
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="image_url" value="{{ old('image_url', $category->image_url) }}">
            </label>
            <label class="grid gap-2 text-sm font-bold">Sort Order
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0">
            </label>
        </div>
        <label class="grid gap-2 text-sm font-bold">Description
            <textarea class="min-h-32 rounded-xl border border-slate-200 px-4 py-3 font-normal" name="description">{{ old('description', $category->description) }}</textarea>
        </label>
        <label class="flex items-center gap-2 text-sm font-bold">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
            Active
        </label>
        <div class="flex gap-3">
            <button class="btn-primary" type="submit">Save Category</button>
            <a class="btn-secondary" href="{{ route('admin.categories.index') }}">Cancel</a>
        </div>
    </form>
@endsection
