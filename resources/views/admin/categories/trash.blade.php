@extends('layouts.admin')

@section('title', 'Deleted Categories - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Trash</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Deleted categories.</h1>
        </div>
        <a class="icon-button h-14 w-14" href="{{ route('admin.categories.index') }}" title="Back to Categories" aria-label="Back to categories">
            <x-icon name="arrow" class="h-5 w-5 rotate-180" />
            <span class="sr-only">Back to Categories</span>
        </a>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($categories as $category)
            <div class="flex flex-col justify-between gap-3 border-b border-slate-100 p-5 md:flex-row md:items-center">
                <div>
                    <div class="font-black text-slate-950">{{ $category->name }}</div>
                    <div class="text-sm text-slate-500">Deleted {{ $category->deleted_at?->format('d M Y H:i') }}</div>
                </div>
                <form method="POST" action="{{ route('admin.categories.restore', $category->id) }}">
                    @csrf
                    @method('PATCH')
                    <button class="icon-button border-slate-950 bg-slate-950 text-white hover:bg-teal-700 hover:text-white" type="submit" title="Restore {{ $category->name }}" aria-label="Restore {{ $category->name }}">
                        <x-icon name="history" class="h-4 w-4" />
                        <span class="sr-only">Restore</span>
                    </button>
                </form>
            </div>
        @empty
            <div class="p-6 text-slate-600">No deleted categories.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $categories->links() }}</div>
@endsection
