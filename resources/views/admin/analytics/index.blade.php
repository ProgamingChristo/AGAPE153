@extends('layouts.admin')

@section('title', 'Analytics - Admin Agape153')

@section('content')
    <div>
        <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Analytics</p>
        <h1 class="mt-3 text-4xl font-black text-slate-950">Buyer funnel and demand.</h1>
    </div>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-xl font-black text-slate-950">Conversion Funnel</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-4">
            @foreach ($funnel as $step)
                <div class="rounded-2xl bg-[#f8faf9] p-5">
                    <div class="text-sm font-bold text-slate-500">{{ $step['label'] }}</div>
                    <div class="mt-3 text-3xl font-black text-teal-800">{{ number_format($step['value']) }}</div>
                </div>
            @endforeach
        </div>
        <div class="mt-5 rounded-xl bg-amber-50 p-4 font-bold text-amber-800">Estimated abandoned cart sessions: {{ number_format($abandonedCartEstimate) }}</div>
    </section>

    <div class="mt-8 grid gap-6 xl:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Product Demand</h2>
            <div class="mt-5 grid gap-3">
                @forelse ($productDemand as $item)
                    <div class="rounded-xl bg-[#f8faf9] p-4">
                        <div class="font-black text-slate-950">{{ $item->product_name }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ number_format($item->quantity) }} unit / Rp{{ number_format($item->revenue, 0, ',', '.') }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">No demand data yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Device Traffic</h2>
            <div class="mt-5 grid gap-3">
                @foreach ($deviceTraffic as $row)
                    <div class="flex justify-between rounded-xl bg-[#f8faf9] p-4"><span>{{ ucfirst($row->device) }}</span><strong>{{ number_format($row->total) }}</strong></div>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Cart Sources</h2>
            <div class="mt-5 grid gap-3">
                @foreach ($sourceTraffic as $row)
                    <div class="rounded-xl bg-[#f8faf9] p-4">
                        <div class="truncate font-bold text-slate-950">{{ $row->source }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ number_format($row->total) }} events</div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
