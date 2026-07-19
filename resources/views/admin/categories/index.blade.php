@extends('layouts.admin')

@section('title', 'Categories - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Categories</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Manage product categories.</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="icon-button h-14 w-14" href="{{ route('admin.categories.trash') }}" title="Trash" aria-label="Open category trash">
                <x-icon name="trash" class="h-5 w-5" />
                <span class="sr-only">Trash</span>
            </a>
            <a class="icon-button h-14 w-14 border-slate-950 bg-slate-950 text-white hover:bg-teal-700 hover:text-white" href="{{ route('admin.categories.create') }}" title="Add Category" aria-label="Add category">
                <x-icon name="plus" class="h-5 w-5" />
                <span class="sr-only">Add Category</span>
            </a>
        </div>
    </div>

    <div class="mt-8 grid gap-4">
        @forelse ($categories as $category)
            @php
                $typeIcon = match ($category->type) {
                    'coffee' => 'coffee',
                    'spice' => 'leaf',
                    'export' => 'truck',
                    default => 'package',
                };
            @endphp

            <article class="lift-card overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="grid gap-5 p-5 lg:grid-cols-[minmax(0,1fr)_72px_72px_120px] lg:items-center">
                    <div class="flex min-w-0 items-center gap-4">
                        <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-teal-50 via-white to-amber-50 text-teal-700">
                            @if ($category->image_url)
                                <img class="h-full w-full object-cover" src="{{ $category->image_url }}" alt="{{ $category->name }}">
                            @else
                                <x-icon :name="$typeIcon" class="h-7 w-7" />
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="truncate text-lg font-black text-slate-950">{{ $category->name }}</div>
                            <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                                <span>{{ $category->parent?->name ?: 'Root category' }}</span>
                                @if ($category->description)
                                    <span class="hidden h-1 w-1 rounded-full bg-slate-300 sm:inline-block"></span>
                                    <span class="hidden max-w-md truncate sm:inline">{{ $category->description }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="lg:justify-self-center">
                        <span class="inline-grid h-11 w-11 place-items-center rounded-full border border-teal-100 bg-teal-50 text-teal-800 shadow-sm" title="{{ ucfirst($category->type) }}" aria-label="Category type {{ ucfirst($category->type) }}">
                            <x-icon :name="$typeIcon" class="h-5 w-5" />
                            <span class="sr-only">{{ ucfirst($category->type) }}</span>
                        </span>
                    </div>

                    <div class="lg:justify-self-center">
                        @if ($category->is_active)
                            <span class="inline-grid h-11 w-11 place-items-center rounded-full border border-emerald-100 bg-emerald-50 text-emerald-700 shadow-sm" title="Active" aria-label="{{ $category->name }} is active">
                                <x-icon name="check" class="h-5 w-5" />
                                <span class="sr-only">Active</span>
                            </span>
                        @else
                            <span class="inline-grid h-11 w-11 place-items-center rounded-full border border-slate-200 bg-slate-50 text-slate-500 shadow-sm" title="Inactive" aria-label="{{ $category->name }} is inactive">
                                <x-icon name="lock" class="h-5 w-5" />
                                <span class="sr-only">Inactive</span>
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 lg:justify-end">
                        <a class="icon-button text-teal-700" href="{{ route('admin.categories.edit', $category) }}" title="Edit {{ $category->name }}" aria-label="Edit {{ $category->name }}">
                            <x-icon name="edit" class="h-4 w-4" />
                        </a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button class="icon-button text-red-700 hover:text-red-700" type="submit" title="Delete {{ $category->name }}" aria-label="Delete {{ $category->name }}">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-600">
                Belum ada kategori produk.
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $categories->links() }}</div>
@endsection
