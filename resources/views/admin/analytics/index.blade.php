@extends('layouts.admin')

@section('title', 'Analytics - Admin Agape153')

@section('content')
    @php
        $trafficCards = [
            ['label' => 'All Page Views', 'value' => $websiteTraffic['total_views'], 'icon' => 'chart', 'color' => '#e64b3c'],
            ['label' => 'Unique Visitors', 'value' => $websiteTraffic['unique_visitors'], 'icon' => 'user', 'color' => '#e9c95a'],
            ['label' => 'Views Today', 'value' => $websiteTraffic['today_views'], 'icon' => 'history', 'color' => '#2d9db7'],
            ['label' => 'Online Now', 'value' => $websiteTraffic['online_now'], 'icon' => 'globe', 'color' => '#0f766e'],
        ];
        $maxTraffic = max((int) collect($trafficTrend)->max('views'), 1);
    @endphp

    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Analytics</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Website traffic and buyer demand.</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Monitor privacy-conscious website activity, popular pages, buyer conversion, and product demand.
            </p>
        </div>
        <div class="inline-flex items-center gap-2 rounded-full border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-black text-teal-800">
            <span class="h-2 w-2 rounded-full bg-teal-600"></span>
            Live traffic
        </div>
    </div>

    <section class="mt-8">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($trafficCards as $card)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" style="border-top: 4px solid {{ $card['color'] }};">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-bold text-slate-500">{{ $card['label'] }}</span>
                        <x-icon name="{{ $card['icon'] }}" class="h-5 w-5" style="color: {{ $card['color'] }};" />
                    </div>
                    <div class="mt-3 text-3xl font-black text-slate-950">{{ number_format($card['value']) }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-teal-700">Last 14 Days</p>
                    <h2 class="mt-2 text-xl font-black text-slate-950">Traffic Trend</h2>
                </div>
                <div class="text-right text-xs font-bold text-slate-500">Page views per day</div>
            </div>

            <div class="mt-6 overflow-x-auto pb-2">
                <div class="flex min-w-[720px] items-end gap-3" style="height: 230px;">
                    @foreach ($trafficTrend as $day)
                        @php
                            $barHeight = $day['views'] > 0
                                ? max(12, (int) round(($day['views'] / $maxTraffic) * 160))
                                : 4;
                        @endphp
                        <div class="flex h-full min-w-0 flex-1 flex-col justify-end text-center">
                            <div class="mb-2 text-xs font-black text-slate-600">{{ number_format($day['views']) }}</div>
                            <div
                                class="mx-auto w-full max-w-8 rounded-t-lg bg-teal-700"
                                style="height: {{ $barHeight }}px;"
                                title="{{ $day['label'] }}: {{ $day['views'] }} views, {{ $day['visitors'] }} visitors"
                            ></div>
                            <div class="mt-2 whitespace-nowrap text-xs font-bold text-slate-400">{{ $day['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-sky-700">Content Performance</p>
            <h2 class="mt-2 text-xl font-black text-slate-950">Most Viewed Pages</h2>
            <div class="mt-5 grid gap-3">
                @forelse ($topPages as $index => $page)
                    <div class="grid grid-cols-[36px_1fr_auto] items-center gap-3 rounded-xl bg-[#f8faf9] p-3">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-white text-sm font-black text-slate-500">{{ $index + 1 }}</span>
                        <div class="min-w-0">
                            <div class="truncate font-black text-slate-950">{{ $page->path }}</div>
                            <div class="mt-1 text-xs font-bold text-slate-500">{{ number_format($page->visitors) }} unique visitors</div>
                        </div>
                        <strong class="text-lg font-black text-sky-800">{{ number_format($page->views) }}</strong>
                    </div>
                @empty
                    <p class="rounded-xl bg-[#f8faf9] p-4 text-sm text-slate-600">Traffic data will appear after visitors browse the website.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex flex-col justify-between gap-3 md:flex-row md:items-end">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.16em] text-amber-700">Buyer Journey</p>
                <h2 class="mt-2 text-xl font-black text-slate-950">Conversion Funnel</h2>
            </div>
            <div class="rounded-xl bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">
                Estimated abandoned cart sessions: {{ number_format($abandonedCartEstimate) }}
            </div>
        </div>
        <div class="mt-6 grid gap-4 md:grid-cols-4">
            @foreach ($funnel as $index => $step)
                <div class="relative rounded-2xl bg-[#f8faf9] p-5">
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Step {{ $index + 1 }}</div>
                    <div class="mt-2 text-sm font-bold text-slate-600">{{ $step['label'] }}</div>
                    <div class="mt-3 text-3xl font-black text-teal-800">{{ number_format($step['value']) }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="mt-8 grid gap-6 xl:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Product Demand</h2>
            <div class="mt-5 grid gap-3">
                @forelse ($productDemand as $item)
                    <div class="rounded-xl bg-[#f8faf9] p-4">
                        <div class="font-black text-slate-950">{{ $item->product_name }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ number_format($item->quantity) }} units / Rp{{ number_format($item->revenue, 0, ',', '.') }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">No demand data yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Visitor Devices</h2>
            <div class="mt-5 grid gap-3">
                @forelse ($deviceTraffic as $row)
                    <div class="flex justify-between rounded-xl bg-[#f8faf9] p-4">
                        <span>{{ ucfirst($row->device) }}</span>
                        <strong>{{ number_format($row->total) }}</strong>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">No device data yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Traffic Sources</h2>
            <div class="mt-5 grid gap-3">
                @forelse ($sourceTraffic as $row)
                    <div class="rounded-xl bg-[#f8faf9] p-4">
                        <div class="truncate font-bold text-slate-950">{{ $row->source }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ number_format($row->total) }} visits</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">No source data yet.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
