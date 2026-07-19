@extends('layouts.app')

@section('title', 'About Agape153 - Indonesian Agriculture International Commodity Trading')
@section('description', 'Agape153 is an Indonesian agricultural commodity trading company connecting spices, herbal roots, and premium products to international buyers.')

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-35" src="https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=1800&q=80" alt="Indonesian agriculture commodity">
        </div>
        <div class="agape-container relative grid min-h-[70vh] items-center gap-10 py-16 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-black text-teal-100">
                    <x-icon name="globe" class="h-4 w-4" />
                    {{ $content['eyebrow'] }}
                </div>
                <h1 class="mt-6 max-w-4xl text-5xl font-black leading-tight sm:text-6xl">{{ $content['title'] }}</h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-200">{{ $content['lead'] }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('products.index', ['export_ready' => 1]) }}" class="btn-primary">
                        <x-icon name="package" class="h-4 w-4" />
                        Export Catalog
                    </a>
                    <a href="{{ route('about', ['lang' => $locale === 'en' ? 'id' : 'en']) }}" class="btn-secondary">
                        <x-icon name="language" class="h-4 w-4" />
                        {{ $locale === 'en' ? 'Read in Bahasa' : 'Read in English' }}
                    </a>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ([['2021', 'Founded'], ['Jakarta', 'Headquarters'], ['FOB/CIF', 'Shipping'], ['International', 'Commodity Trading']] as [$value, $label])
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-6 backdrop-blur lift-card">
                        <div class="text-3xl font-black text-teal-200">{{ $value }}</div>
                        <div class="mt-2 text-sm font-bold text-slate-200">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad bg-white">
        <div class="agape-container grid gap-10 lg:grid-cols-[0.85fr_1.15fr]">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">The Journey</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950 sm:text-4xl">From local sourcing roots to international trading trust.</h2>
            </div>
            <div class="grid gap-4">
                @foreach ($content['overview'] as $index => $paragraph)
                    <div class="lift-card rounded-2xl border border-slate-200 bg-[#f8faf9] p-6">
                        <div class="mb-3 inline-grid h-9 w-9 place-items-center rounded-xl bg-teal-700 text-sm font-black text-white">0{{ $index + 1 }}</div>
                        <p class="leading-7 text-slate-600">{{ $paragraph }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#edf7f4]">
        <div class="agape-container grid gap-6 lg:grid-cols-2">
            <div class="lift-card rounded-2xl border border-teal-100 bg-white p-8">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-700 text-white">
                    <x-icon name="check" class="h-6 w-6" />
                </div>
                <h2 class="mt-5 text-2xl font-black text-slate-950">Vision</h2>
                <p class="mt-4 leading-7 text-slate-600">{{ $content['vision'] }}</p>
            </div>
            <div class="lift-card rounded-2xl border border-teal-100 bg-white p-8">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white">
                    <x-icon name="arrow" class="h-6 w-6" />
                </div>
                <h2 class="mt-5 text-2xl font-black text-slate-950">Mission</h2>
                <p class="mt-4 leading-7 text-slate-600">{{ $content['mission'] }}</p>
            </div>
        </div>
    </section>

    <section class="section-pad bg-white">
        <div class="agape-container">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Commodity Focus</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-950 sm:text-4xl">Products aligned with international demand.</h2>
                </div>
                <a class="btn-secondary" href="{{ route('products.index') }}">
                    <x-icon name="search" class="h-4 w-4" />
                    Browse Products
                </a>
            </div>
            <div class="mt-8 grid gap-5 md:grid-cols-3">
                @foreach ([
                    ['icon' => 'leaf', 'title' => 'Spices', 'items' => 'Nutmeg, mace, white pepper, black pepper, cloves, and other Indonesian spices prepared for culinary, industrial, and wellness markets.'],
                    ['icon' => 'coffee', 'title' => 'Herbal Roots', 'items' => 'Ginger, turmeric, galangal, and Java curcuma with controlled moisture, purity, and functional applications.'],
                    ['icon' => 'boxes', 'title' => 'Agricultural Products', 'items' => 'Papaya leaves, banana leaves, banana stem, and sustainable agriculture-derived products for international buyers.'],
                ] as $card)
                    <article class="lift-card rounded-2xl border border-slate-200 bg-[#f8faf9] p-6">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-teal-700 shadow-sm">
                            <x-icon :name="$card['icon'] === 'boxes' ? 'package' : $card['icon']" class="h-6 w-6" />
                        </div>
                        <h3 class="mt-5 text-xl font-black text-slate-950">{{ $card['title'] }}</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $card['items'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad bg-slate-950 text-white">
        <div class="agape-container">
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-300">Operating Pillars</p>
            <div class="mt-6 grid gap-5 lg:grid-cols-3">
                @foreach ($content['pillars'] as $index => $pillar)
                    <article class="lift-card rounded-2xl border border-white/10 bg-white/10 p-6 backdrop-blur">
                        <div class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-teal-400 text-slate-950">
                            <x-icon :name="['shield', 'truck', 'globe'][$index]" class="h-5 w-5" />
                        </div>
                        <h3 class="mt-5 text-xl font-black">{{ $pillar['title'] }}</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-200">{{ $pillar['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad bg-white">
        <div class="agape-container grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Contact Information</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950 sm:text-4xl">Ready for sourcing and partnership discussions.</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 p-5">
                    <x-icon name="mail" class="h-5 w-5 text-teal-700" />
                    <div class="mt-3 text-sm text-slate-500">Email</div>
                    <a class="mt-1 block break-words font-black text-slate-950 hover:text-teal-700" href="mailto:{{ $siteContact['email'] }}">{{ $siteContact['email'] }}</a>
                </div>
                <div class="rounded-2xl border border-slate-200 p-5">
                    <x-icon name="phone" class="h-5 w-5 text-teal-700" />
                    <div class="mt-3 text-sm text-slate-500">Phone / WhatsApp</div>
                    <a class="mt-1 block font-black text-slate-950 hover:text-teal-700" href="https://wa.me/{{ preg_replace('/\D+/', '', $siteContact['whatsapp']) }}" target="_blank" rel="noopener">{{ $siteContact['phone'] }}</a>
                </div>
                <div class="rounded-2xl border border-slate-200 p-5">
                    <x-icon name="map" class="h-5 w-5 text-teal-700" />
                    <div class="mt-3 text-sm text-slate-500">Location</div>
                    <div class="mt-1 font-black text-slate-950">Jakarta, Indonesia</div>
                </div>
            </div>
            <div class="mt-5 flex flex-wrap gap-2 lg:col-start-2">
                <a class="btn-secondary" href="{{ $siteContact['youtube_url'] }}" target="_blank" rel="noopener"><x-icon name="globe" class="h-4 w-4" />YouTube</a>
                <a class="btn-secondary" href="{{ $siteContact['instagram_url'] }}" target="_blank" rel="noopener"><x-icon name="at" class="h-4 w-4" />Instagram</a>
                <a class="btn-secondary" href="{{ $siteContact['facebook_url'] }}" target="_blank" rel="noopener"><x-icon name="globe" class="h-4 w-4" />Facebook</a>
                <a class="btn-secondary" href="{{ $siteContact['linkedin_url'] }}" target="_blank" rel="noopener"><x-icon name="globe" class="h-4 w-4" />LinkedIn</a>
                <a class="btn-secondary" href="{{ $siteContact['tiktok_url'] }}" target="_blank" rel="noopener"><x-icon name="music" class="h-4 w-4" />TikTok</a>
                <a class="btn-secondary" href="{{ $siteContact['threads_url'] }}" target="_blank" rel="noopener"><x-icon name="at" class="h-4 w-4" />Threads</a>
            </div>
        </div>
    </section>
@endsection
