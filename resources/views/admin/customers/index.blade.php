@extends('layouts.admin')

@section('title', 'Customers - Admin Agape153')

@section('content')
    <div>
        <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Customers</p>
        <h1 class="mt-3 text-4xl font-black text-slate-950">Buyer profiles.</h1>
    </div>

    <form class="mt-8 flex gap-3 rounded-2xl border border-slate-200 bg-white p-4" method="GET">
        <input class="w-full rounded-xl border border-slate-200 px-4 py-3" name="q" value="{{ request('q') }}" placeholder="Search buyer">
        <button class="btn-primary" type="submit">Search</button>
    </form>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($customers as $customer)
            <a class="grid gap-4 border-b border-slate-100 p-5 transition hover:bg-teal-50 md:grid-cols-[1fr_120px_160px_140px] md:items-center" href="{{ route('admin.customers.show', $customer) }}">
                <div>
                    <div class="font-black text-slate-950">{{ $customer->name }}</div>
                    <div class="mt-1 text-sm text-slate-500">{{ $customer->email }} / {{ $customer->company_name ?: 'No company' }}</div>
                </div>
                <div class="font-black text-slate-950">{{ number_format($customer->orders_count) }} orders</div>
                <div class="font-black text-teal-800">Rp{{ number_format($customer->total_spend ?? 0, 0, ',', '.') }}</div>
                <div class="text-sm font-bold text-slate-500">{{ $customer->orders_count > 1 ? 'Repeat buyer' : 'New buyer' }}</div>
            </a>
        @empty
            <div class="p-6 text-slate-600">No customers yet.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $customers->links() }}</div>
@endsection
