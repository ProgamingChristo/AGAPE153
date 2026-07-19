@extends('layouts.app')

@section('title', 'Agape153 - Indonesian Commodity Trading Desk')
@section('description', 'Agape153 supplies Indonesian spices, coffee, and agriculture commodities for local buyers and international trading partners.')

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "Agape153",
    "url": "{{ url('/') }}",
    "sameAs": [
        "{{ $siteContact['youtube_url'] }}",
        "{{ $siteContact['instagram_url'] }}",
        "{{ $siteContact['facebook_url'] }}",
        "{{ $siteContact['linkedin_url'] }}",
        "{{ $siteContact['tiktok_url'] }}",
        "{{ $siteContact['threads_url'] }}"
    ],
    "contactPoint": {
        "@@type": "ContactPoint",
        "telephone": "{{ $siteContact['phone'] }}",
        "contactType": "sales"
    }
}
</script>
@endsection

@section('content')
    @php
        $heroSlides = collect($siteAppearance['hero_slides'] ?? [])
            ->filter()
            ->unique()
            ->values();

        if ($heroSlides->isEmpty()) {
            $heroSlides = collect([
                $siteAppearance['hero_image_url'] ?? 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=1800&q=80',
            ]);
        }
    @endphp
    <section class="relative overflow-hidden bg-[#101820] text-white">
        <div class="hero-slideshow absolute inset-0" style="--hero-slide-duration: {{ max(1, $heroSlides->count()) * 6 }}s;">
            @foreach ($heroSlides as $index => $slide)
                <img class="{{ $heroSlides->count() > 1 ? 'hero-slide' : 'absolute inset-0 h-full w-full object-cover opacity-[0.48]' }}" style="--hero-slide-delay: {{ $index * 6 }}s;" src="{{ $slide }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="Indonesian commodity sourcing {{ $index + 1 }}">
            @endforeach
            <div class="absolute inset-0 bg-[#101820]/78"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_22%_24%,rgba(233,201,90,0.22),transparent_30%),radial-gradient(circle_at_78%_72%,rgba(45,157,183,0.24),transparent_32%)]"></div>
        </div>

        <div class="agape-container relative grid min-h-[calc(100vh-7rem)] items-end py-8">
            <div class="max-w-5xl pb-4" data-reveal>
                <div class="inline-flex border border-white/15 bg-white/10 px-3 py-2 text-xs font-black uppercase tracking-[0.2em] text-amber-200">
                    {{ $siteAppearance['hero_badge'] ?? 'Indonesian Commodity Trading Desk' }}
                </div>
                <h1 class="mt-5 max-w-5xl leading-[0.88]">
                    <span class="block text-6xl font-black tracking-wide text-[#e9c95a] drop-shadow-[0_10px_28px_rgba(0,0,0,0.28)] sm:text-7xl lg:text-8xl">AGAPE</span>
                    <span class="mt-4 flex flex-wrap gap-3 text-5xl font-black leading-none sm:text-6xl lg:text-7xl" aria-label="153">
                        <span class="grid min-h-20 min-w-20 place-items-center border-2 border-white/80 bg-[#e64b3c] px-4 text-slate-950 shadow-[8px_8px_0_rgba(0,0,0,0.22)]">1</span>
                        <span class="grid min-h-20 min-w-20 place-items-center border-2 border-white/80 bg-[#e9c95a] px-4 text-slate-950 shadow-[8px_8px_0_rgba(0,0,0,0.22)]">5</span>
                        <span class="grid min-h-20 min-w-20 place-items-center border-2 border-white/80 bg-[#2d9db7] px-4 text-slate-950 shadow-[8px_8px_0_rgba(0,0,0,0.22)]">3</span>
                    </span>
                </h1>
                <p class="mt-6 max-w-2xl text-xl font-black leading-8 text-white">{{ $siteAppearance['hero_title'] ?? 'Spices, coffee, and agricultural products from Indonesia.' }}</p>
                <p class="mt-4 max-w-2xl leading-8 text-slate-200">{{ $siteAppearance['hero_subtitle'] ?? 'A digital sourcing flow for local buyers, horeca, distributors, exporters, and international importers.' }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="btn-primary">
                        <x-icon name="package" class="h-4 w-4" />
                        Open Trading Catalog
                    </a>
                    <a href="#contact" class="btn-secondary">
                        <x-icon name="message" class="h-4 w-4" />
                        Request Supply
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-5">
        <div class="agape-container grid gap-3 md:grid-cols-4">
            @foreach ([
                ['n' => '01', 'title' => 'Browse'],
                ['n' => '02', 'title' => 'Cart'],
                ['n' => '03', 'title' => 'Online / WA'],
                ['n' => '04', 'title' => 'Invoice'],
            ] as $step)
                <div class="trade-panel p-4" data-reveal>
                    <div class="text-3xl font-black text-slate-950">{{ $step['n'] }}</div>
                    <div class="mt-1 text-sm font-black uppercase tracking-[0.14em] text-slate-600">{{ $step['title'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section id="about" class="section-pad bg-[#f4f6f3]">
        <div class="agape-container grid gap-6 lg:grid-cols-[0.78fr_1.22fr]">
            <div data-reveal>
                <p class="section-kicker"><x-icon name="globe" class="h-4 w-4" />Company</p>
                <h2 class="mt-3 text-4xl font-black leading-tight text-slate-950 sm:text-5xl">Built like a procurement desk, not a brochure.</h2>
                <p class="mt-5 leading-8 text-slate-600">Agape153 turns product discovery, buyer inquiry, online payment, order tracking, and invoice download into one connected user journey.</p>
                <a class="btn-secondary mt-6" href="{{ route('about') }}">
                    <x-icon name="arrow" class="h-4 w-4" />
                    Company Profile
                </a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    ['icon' => 'leaf', 'title' => 'Spices', 'body' => 'Nutmeg, mace, pepper, cloves, and other Indonesian origin products.'],
                    ['icon' => 'coffee', 'title' => 'Coffee', 'body' => 'Arabica and robusta catalog paths for local and international buyers.'],
                    ['icon' => 'truck', 'title' => 'Export', 'body' => 'Built for destination, packing, MOQ, logistics, and quotation discussion.'],
                    ['icon' => 'orders', 'title' => 'Invoice', 'body' => 'Member order history, payment status, and downloadable PDF invoice.'],
                ] as $card)
                    <article class="trade-panel p-5" data-reveal>
                        <x-icon :name="$card['icon']" class="h-6 w-6 text-teal-700" />
                        <h3 class="mt-4 text-xl font-black text-slate-950">{{ $card['title'] }}</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $card['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#101820] text-white">
        <div class="agape-container">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end" data-reveal>
                <div>
                    <p class="section-kicker text-amber-200"><x-icon name="package" class="h-4 w-4" />Commodity Lines</p>
                    <h2 class="mt-3 max-w-3xl text-4xl font-black sm:text-5xl">Choose a category and move straight into sourcing.</h2>
                </div>
                <a class="btn-secondary" href="{{ route('products.index') }}">All Products</a>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group border border-white/10 bg-white text-slate-950" data-reveal>
                        <img class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-105" src="{{ $category->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $category->name }}" loading="lazy" decoding="async">
                        <div class="logo-stripe"><span></span><span></span><span></span></div>
                        <div class="p-5">
                            <span class="status-pill text-teal-700">{{ $category->type }}</span>
                            <h3 class="mt-3 text-xl font-black">{{ $category->name }}</h3>
                            <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $category->description ?: 'Explore available Agape153 products in this category.' }}</p>
                        </div>
                    </a>
                @empty
                    <div class="border border-white/10 bg-white/10 p-6 text-slate-200">Categories will appear here after admin publishes them.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-pad bg-white">
        <div class="agape-container">
            <div class="grid gap-6 lg:grid-cols-[0.75fr_1.25fr]">
                <div data-reveal>
                    <p class="section-kicker"><x-icon name="coffee" class="h-4 w-4" />Featured Market</p>
                    <h2 class="mt-3 text-4xl font-black leading-tight text-slate-950 sm:text-5xl">Featured SKUs as a trade list.</h2>
                    <p class="mt-4 leading-8 text-slate-600">A denser buyer view with product image, category, MOQ, stock, export readiness, and price.</p>
                    <a class="btn-primary mt-6" href="{{ route('products.index') }}">Open Full Catalog</a>
                </div>

                <div class="grid gap-3">
                    @forelse ($featuredProducts as $product)
                        <a class="market-row" href="{{ route('products.show', $product) }}" data-reveal>
                            <img class="h-20 w-20 shrink-0 object-cover" src="{{ $product->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $product->name }}" loading="lazy" decoding="async">
                            <div class="grid gap-3 md:grid-cols-[1fr_120px_120px_120px] md:items-center">
                                <div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="status-pill text-teal-700">{{ $product->category->name }}</span>
                                        @if ($product->export_ready)
                                            <span class="status-pill border-amber-200 bg-amber-50 text-amber-800">Export</span>
                                        @endif
                                    </div>
                                    <h3 class="mt-2 font-black text-slate-950">{{ $product->name }}</h3>
                                </div>
                                <div class="text-sm font-bold text-slate-600">MOQ<br><span class="text-slate-950">{{ $product->min_order_quantity }} {{ $product->unit }}</span></div>
                                <div class="text-sm font-bold text-slate-600">Stock<br><span class="text-slate-950">{{ $product->stock_quantity }} {{ $product->unit }}</span></div>
                                <div class="font-black text-teal-800">{{ $product->formattedPrice() }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="trade-panel p-6 text-slate-600">Featured products will appear here after admin marks products as featured.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section id="export" class="section-pad bg-[#f4f6f3]">
        <div class="agape-container grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1" data-reveal>
                <p class="section-kicker"><x-icon name="truck" class="h-4 w-4" />Export Flow</p>
                <h2 class="mt-3 text-4xl font-black text-slate-950">From inquiry to invoice.</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-3 lg:col-span-2">
                @foreach ([
                    ['title' => 'Buyer sends requirements', 'body' => 'Product, grade, quantity, destination, and packing needs.'],
                    ['title' => 'Admin follows up', 'body' => 'Contact form and order data stay visible in admin.'],
                    ['title' => 'Payment and invoice', 'body' => 'Online payment popup, WhatsApp path, and PDF invoice for members.'],
                ] as $item)
                    <article class="trade-panel p-5" data-reveal>
                        <h3 class="text-lg font-black text-slate-950">{{ $item['title'] }}</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @if ($siteAppearance['show_gallery'] ?? true)
        <section class="section-pad bg-white">
            <div class="agape-container">
                <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end" data-reveal>
                    <div>
                        <p class="section-kicker"><x-icon name="leaf" class="h-4 w-4" />Gallery</p>
                        <h2 class="mt-3 text-4xl font-black text-slate-950">Visual sourcing board.</h2>
                    </div>
                </div>
                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($galleries as $gallery)
                        <figure class="trade-panel overflow-hidden" data-reveal>
                            <img class="h-60 w-full object-cover" src="{{ $gallery->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $gallery->title }}" loading="lazy" decoding="async">
                            <figcaption class="p-4 text-sm font-bold text-slate-800">{{ $gallery->title }}</figcaption>
                        </figure>
                    @empty
                        <div class="trade-panel p-6 text-slate-600 sm:col-span-2 lg:col-span-3">Gallery items will appear after admin uploads them.</div>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    @if ($siteAppearance['show_testimonials'] ?? true)
        <section class="section-pad bg-[#101820] text-white">
            <div class="agape-container">
                <p class="section-kicker text-amber-200"><x-icon name="message" class="h-4 w-4" />Testimonials</p>
                <div class="mt-6 grid gap-5 md:grid-cols-3">
                    @forelse ($testimonials as $testimonial)
                        <blockquote class="border border-white/10 bg-white/10 p-6" data-reveal>
                            <p class="text-sm leading-7 text-slate-100">"{{ $testimonial->message }}"</p>
                            <footer class="mt-5 font-black">{{ $testimonial->name }} <span class="font-medium text-slate-300">/ {{ $testimonial->country }}</span></footer>
                        </blockquote>
                    @empty
                        <div class="border border-white/10 bg-white/10 p-6 text-slate-200 md:col-span-3">Testimonials will appear after admin publishes them.</div>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    <section id="faq" class="section-pad bg-white">
        <div class="agape-container grid gap-8 lg:grid-cols-[0.75fr_1.25fr]">
            <div data-reveal>
                <p class="section-kicker"><x-icon name="search" class="h-4 w-4" />FAQ</p>
                <h2 class="mt-3 text-4xl font-black text-slate-950">Buyer questions, answered fast.</h2>
            </div>
            <div class="grid gap-3">
                @forelse ($faqs as $faq)
                    <details class="border border-slate-200 bg-[#f8faf9] p-5 transition open:bg-white open:shadow-sm" data-reveal>
                        <summary class="cursor-pointer font-black text-slate-950">{{ $faq->question }}</summary>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $faq->answer }}</p>
                    </details>
                @empty
                    <div class="trade-panel p-6 text-slate-600">FAQ content will appear after admin publishes it.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="contact" class="section-pad bg-[#f4f6f3]">
        <div class="agape-container grid gap-8 lg:grid-cols-[1fr_0.9fr]">
            <form class="trade-panel p-6 md:p-8" action="{{ route('contact.store') }}" method="POST" data-reveal>
                @csrf
                <div class="logo-stripe -mx-6 -mt-6 mb-6 md:-mx-8 md:-mt-8"><span></span><span></span><span></span></div>
                <p class="section-kicker"><x-icon name="message" class="h-4 w-4" />Contact Form</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950">Send sourcing request.</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <input class="field-input" name="name" value="{{ old('name') }}" placeholder="Full name" required>
                    <input class="field-input" type="email" name="email" value="{{ old('email') }}" placeholder="Email address" required>
                    <input class="field-input" name="phone" value="{{ old('phone') }}" placeholder="Phone / WhatsApp">
                    <input class="field-input" name="company_name" value="{{ old('company_name') }}" placeholder="Company name">
                    <select class="field-input md:col-span-2" name="interest">
                        <option value="">Select interest</option>
                        @foreach (['Spices', 'Arabica Coffee', 'Robusta Coffee', 'Export Partnership', 'Bulk Inquiry', 'Other'] as $interest)
                            <option value="{{ $interest }}" @selected(old('interest') === $interest)>{{ $interest }}</option>
                        @endforeach
                    </select>
                </div>
                <textarea class="field-input mt-4 min-h-36" name="message" placeholder="Product, grade, quantity, destination, packing, timeline..." required>{{ old('message') }}</textarea>
                <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <button class="btn-primary" type="submit"><x-icon name="message" class="h-4 w-4" />Send to Admin</button>
                    <a class="btn-secondary" href="https://wa.me/{{ preg_replace('/\D+/', '', $siteContact['whatsapp']) }}?text={{ rawurlencode('Hello Agape153, I would like to ask about your product catalog.') }}" target="_blank" rel="noopener"><x-icon name="phone" class="h-4 w-4" />WhatsApp</a>
                </div>
            </form>

            <div data-reveal>
                <x-logo />
                <p class="section-kicker mt-7"><x-icon name="phone" class="h-4 w-4" />Contact Desk</p>
                <h2 class="mt-3 text-4xl font-black text-slate-950">Talk to Agape153.</h2>
                <div class="mt-6 grid gap-3">
                    <a class="trade-panel flex items-center gap-3 p-4" href="mailto:{{ $siteContact['email'] }}">
                        <x-icon name="mail" class="h-5 w-5 text-teal-700" />
                        <span class="font-black text-slate-950">{{ $siteContact['email'] }}</span>
                    </a>
                    <a class="trade-panel flex items-center gap-3 p-4" href="https://wa.me/{{ preg_replace('/\D+/', '', $siteContact['whatsapp']) }}" target="_blank" rel="noopener">
                        <x-icon name="phone" class="h-5 w-5 text-teal-700" />
                        <span class="font-black text-slate-950">{{ $siteContact['phone'] }}</span>
                    </a>
                </div>
                <div class="mt-6 flex flex-wrap gap-2">
                    <a class="btn-secondary" href="{{ $siteContact['youtube_url'] }}" target="_blank" rel="noopener">YouTube</a>
                    <a class="btn-secondary" href="{{ $siteContact['instagram_url'] }}" target="_blank" rel="noopener">Instagram</a>
                    <a class="btn-secondary" href="{{ $siteContact['linkedin_url'] }}" target="_blank" rel="noopener">LinkedIn</a>
                    <a class="btn-secondary" href="{{ $siteContact['tiktok_url'] }}" target="_blank" rel="noopener">TikTok</a>
                    <a class="btn-secondary" href="{{ $siteContact['threads_url'] }}" target="_blank" rel="noopener">Threads</a>
                </div>
            </div>
        </div>
    </section>
@endsection
