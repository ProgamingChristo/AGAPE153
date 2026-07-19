@extends('layouts.admin')

@section('title', 'Stock Movement Logs - Admin Agape153')

@section('content')
    <div>
        <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Inventory</p>
        <h1 class="mt-3 text-4xl font-black text-slate-950">Stock movement history.</h1>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($movements as $movement)
            <div class="grid gap-4 border-b border-slate-100 p-5 md:grid-cols-[1fr_120px_120px_1fr] md:items-center">
                <div>
                    <div class="font-black text-slate-950">{{ $movement->product?->name ?? 'Deleted product' }}</div>
                    <div class="mt-1 text-sm text-slate-500">{{ $movement->created_at->format('d M Y H:i') }} / {{ $movement->user?->name ?? 'System' }}</div>
                </div>
                <div class="font-black {{ $movement->delta >= 0 ? 'text-teal-800' : 'text-red-700' }}">{{ $movement->delta >= 0 ? '+' : '' }}{{ $movement->delta }}</div>
                <div class="text-sm font-bold text-slate-700">{{ $movement->quantity_before }} -> {{ $movement->quantity_after }}</div>
                <div class="text-sm text-slate-600">{{ $movement->type }} / {{ $movement->reason }}</div>
            </div>
        @empty
            <div class="p-6 text-slate-600">No stock movements yet.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $movements->links() }}</div>
@endsection
