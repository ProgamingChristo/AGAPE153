@extends('layouts.admin')

@section('title', 'Categories - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Categories</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Manage product categories.</h1>
        </div>
        <a class="btn-primary" href="{{ route('admin.categories.create') }}">Add Category</a>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @foreach ($categories as $category)
            <div class="grid gap-3 border-b border-slate-100 px-4 py-4 text-sm md:grid-cols-[1fr_120px_160px_120px] md:items-center">
                <div>
                    <div class="font-black text-slate-950">{{ $category->name }}</div>
                    <div class="text-slate-500">{{ $category->parent?->name ?: 'Root category' }}</div>
                </div>
                <div class="font-bold">{{ $category->type }}</div>
                <div>{{ $category->is_active ? 'Active' : 'Inactive' }}</div>
                <div class="flex gap-2">
                    <a class="font-black text-teal-700" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                        @csrf
                        @method('DELETE')
                        <button class="font-black text-red-700" type="submit">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $categories->links() }}</div>
@endsection
