@extends('layouts.admin')

@section('title', 'Deleted Products - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Trash</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Deleted products.</h1>
        </div>
        <a class="btn-secondary" href="{{ route('admin.products.index') }}">Back to Products</a>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($products as $product)
            <div class="flex flex-col justify-between gap-3 border-b border-slate-100 p-5 md:flex-row md:items-center">
                <div>
                    <div class="font-black text-slate-950">{{ $product->name }}</div>
                    <div class="text-sm text-slate-500">Deleted {{ $product->deleted_at?->format('d M Y H:i') }}</div>
                </div>
                <form method="POST" action="{{ route('admin.products.restore', $product->id) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn-primary" type="submit">Restore</button>
                </form>
            </div>
        @empty
            <div class="p-6 text-slate-600">No deleted products.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $products->links() }}</div>
@endsection
