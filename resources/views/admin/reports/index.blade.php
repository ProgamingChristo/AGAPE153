@extends('layouts.admin')

@section('title', 'Sales & Finance Reports - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Reports</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Sales and finance.</h1>
            <p class="mt-3 text-sm font-semibold text-slate-500">{{ $report['start']->format('d M Y') }} - {{ $report['end']->format('d M Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="btn-secondary" href="{{ route('admin.reports.csv', request()->query()) }}">
                <x-icon name="download" class="h-4 w-4" />
                Download CSV
            </a>
            <a class="btn-primary" href="{{ route('admin.reports.pdf', request()->query()) }}">
                <x-icon name="download" class="h-4 w-4" />
                Download PDF
            </a>
        </div>
    </div>

    <form class="mt-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_1fr_auto]" method="GET">
        <label class="grid gap-2 text-sm font-bold text-slate-700">Start Date
            <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-teal-500" type="date" name="start_date" value="{{ request('start_date', $report['start']->toDateString()) }}">
        </label>
        <label class="grid gap-2 text-sm font-bold text-slate-700">End Date
            <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal outline-none focus:border-teal-500" type="date" name="end_date" value="{{ request('end_date', $report['end']->toDateString()) }}">
        </label>
        <button class="btn-secondary self-end" type="submit"><x-icon name="search" class="h-4 w-4" />Apply</button>
    </form>

    <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Total Revenue', $report['totalRevenue'], 'teal'],
            ['Paid Revenue', $report['paidRevenue'], 'emerald'],
            ['Unpaid Amount', $report['unpaidRevenue'], 'amber'],
            ['Average Order', $report['averageOrderValue'], 'slate'],
        ] as [$label, $value, $tone])
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="text-sm font-bold text-slate-500">{{ $label }}</div>
                <div class="mt-3 text-2xl font-black text-slate-950">Rp{{ number_format($value, 0, ',', '.') }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-sm font-bold text-slate-500">Valid Orders</div>
            <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($report['orderCount']) }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-sm font-bold text-slate-500">Paid Orders</div>
            <div class="mt-3 text-3xl font-black text-teal-800">{{ number_format($report['paidOrderCount']) }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-sm font-bold text-slate-500">Cancelled Orders</div>
            <div class="mt-3 text-3xl font-black text-red-700">{{ number_format($report['cancelledOrderCount']) }}</div>
        </div>
    </div>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-black text-slate-950">Monthly revenue chart</h2>
            <span class="text-sm font-bold text-slate-500">Non-cancelled orders</span>
        </div>
        <div class="mt-8 flex h-72 items-end gap-3 overflow-x-auto border-b border-l border-slate-200 px-3 pb-3">
            @foreach ($report['monthly'] as $month)
                @php
                    $height = max(8, ((float) $month['revenue'] / $report['maxMonthlyRevenue']) * 230);
                @endphp
                <div class="flex min-w-24 flex-1 flex-col items-center justify-end gap-2">
                    <div class="text-xs font-black text-slate-700">Rp{{ number_format($month['revenue'] / 1000000, 1, ',', '.') }}M</div>
                    <div class="w-full rounded-t-xl bg-teal-700 transition hover:bg-teal-800" style="height: {{ $height }}px" title="{{ $month['label'] }}"></div>
                    <div class="text-center text-xs font-bold text-slate-500">{{ $month['label'] }}<br>{{ $month['orders'] }} orders</div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="mt-8 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Top Products</h2>
            <div class="mt-5 grid gap-3">
                @forelse ($report['topProducts'] as $product)
                    <div class="flex justify-between gap-4 rounded-xl bg-[#f8faf9] p-4">
                        <div>
                            <div class="font-black text-slate-950">{{ $product['name'] }}</div>
                            <div class="mt-1 text-sm text-slate-500">{{ number_format($product['quantity']) }} unit sold</div>
                        </div>
                        <div class="font-black text-teal-800">Rp{{ number_format($product['revenue'], 0, ',', '.') }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">No product sales in this period.</p>
                @endforelse
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 bg-[#f8faf9] p-5">
                <h2 class="text-xl font-black text-slate-950">Recent Sales</h2>
            </div>
            @forelse ($report['orders'] as $order)
                <a class="grid gap-3 border-b border-slate-100 p-4 transition hover:bg-teal-50 md:grid-cols-[1fr_140px_130px] md:items-center" href="{{ route('admin.orders.show', $order) }}">
                    <div>
                        <div class="font-black text-slate-950">{{ $order->order_number }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ $order->customer_name }} / {{ $order->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="text-sm font-black uppercase text-slate-600">{{ $order->payment_status }}</div>
                    <div class="font-black text-teal-800">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</div>
                </a>
            @empty
                <div class="p-6 text-sm text-slate-600">No orders in this period.</div>
            @endforelse
        </section>
    </div>
@endsection
