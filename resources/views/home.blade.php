@extends('layouts.app')

@section('title', $siteText['meta.title'] ?? 'Agape153 - Indonesian Commodity Trading Desk')
@section('description', $siteText['meta.description'] ?? 'Agape153 supplies Indonesian spices, coffee, and agriculture commodities for local buyers and international trading partners.')

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
        $t = $siteText ?? [];
        $quoteLabel = $t['product.quote_label'] ?? 'Please contact or drop us email';
        $quoteHint = $t['product.quote_hint'] ?? 'Pricing depends on MOQ';
        $slideSeconds = 2;
        $heroSlides = collect([
            'nutmeg-pala-whole-mace.jpg',
            'cloves-cengkeh.jpg',
            'white-pepper.jpg',
            'black-pepper.jpg',
            'dried-chili-cabe-kering.jpg',
            'chili-powder-cabe-bubuk.jpg',
            'garlic-bawang-putih.jpg',
            'turmeric-kunyit.jpg',
            'galangal-lengkuas.jpg',
            'curcuma-xanthorrhiza-temulawak.jpg',
            'papaya-leaves-daun-papaya.jpg',
            'banana-stem-batang-pisang.jpg',
            'robusta-green-beans.jpg',
            'arabica-green-beans.jpg',
        ])->map(fn ($image) => asset('images/catalog/'.$image));
    @endphp
    <section class="relative overflow-hidden bg-[#101820] text-white">
        <div class="hero-slideshow absolute inset-0" style="--hero-slide-duration: {{ max(1, $heroSlides->count()) * $slideSeconds }}s;">
            @foreach ($heroSlides as $index => $slide)
                <img class="hero-slide" style="--hero-slide-delay: {{ $index * $slideSeconds }}s;" src="{{ $slide }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="Agape153 commodity product {{ $index + 1 }}" fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}">
            @endforeach
            <div class="absolute inset-0 bg-gradient-to-r from-[#101820]/76 via-[#101820]/58 to-[#101820]/46"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_22%_24%,rgba(233,201,90,0.18),transparent_30%),radial-gradient(circle_at_78%_72%,rgba(45,157,183,0.18),transparent_34%)]"></div>
        </div>

        <div class="agape-container relative grid min-h-[calc(100vh-7rem)] items-end py-8">
            <div class="max-w-5xl pb-4" data-reveal>
                <div class="inline-flex border border-white/15 bg-white/10 px-3 py-2 text-xs font-black uppercase tracking-[0.2em] text-amber-200">
                    {{ $t['hero.badge'] ?? 'Indonesian spices and coffee supplier' }}
                </div>
                <div class="mt-5">
                    <x-logo variant="hero" class="drop-shadow-[0_18px_34px_rgba(0,0,0,0.32)]" />
                </div>
                <p class="mt-6 max-w-2xl text-xl font-black leading-8 text-white">{{ $t['hero.title'] ?? 'Agape153' }}</p>
                <p class="mt-4 max-w-2xl leading-8 text-slate-200">{{ $t['hero.subtitle'] ?? 'A curated product catalog for Indonesian spices, coffee, and agricultural commodities.' }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="btn-primary">
                        <x-icon name="package" class="h-4 w-4" />
                        {{ $t['hero.catalog_button'] ?? 'Open Product Catalog' }}
                    </a>
                    <a href="#contact" class="btn-secondary">
                        <x-icon name="message" class="h-4 w-4" />
                        {{ $t['hero.request_button'] ?? 'Request Supply' }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    @auth
    <section class="bg-white py-5">
        <div class="agape-container grid gap-3 md:grid-cols-4">
            @foreach ([
                ['n' => '01', 'title' => $t['steps.browse'] ?? 'Browse'],
                ['n' => '02', 'title' => $t['steps.cart'] ?? 'Cart'],
                ['n' => '03', 'title' => $t['steps.payment'] ?? 'Online / WhatsApp'],
                ['n' => '04', 'title' => $t['steps.invoice'] ?? 'Invoice'],
            ] as $step)
                <div class="trade-panel p-4" data-reveal>
                    <div class="text-3xl font-black text-slate-950">{{ $step['n'] }}</div>
                    <div class="mt-1 text-sm font-black uppercase tracking-[0.14em] text-slate-600">{{ $step['title'] }}</div>
                </div>
            @endforeach
        </div>
    </section>
    @endauth

    <section id="about" class="section-pad bg-[#f4f6f3]">
        <div class="agape-container grid gap-6 lg:grid-cols-[0.78fr_1.22fr]">
            <div data-reveal>
                <p class="section-kicker"><x-icon name="globe" class="h-4 w-4" />{{ $t['home.company_kicker'] ?? 'Company' }}</p>
                <h2 class="mt-3 text-4xl font-black leading-tight text-slate-950 sm:text-5xl">{{ $t['home.company_title'] ?? 'Built like a sourcing desk, made easy for buyers.' }}</h2>
                <p class="mt-5 leading-8 text-slate-600">{{ $t['home.company_body'] ?? 'Agape153 connects product discovery, buyer inquiry, online payment, shipping updates, and invoice download in one practical journey.' }}</p>
                <a class="btn-secondary mt-6" href="{{ route('about') }}">
                    <x-icon name="arrow" class="h-4 w-4" />
                    {{ $t['home.company_button'] ?? 'Company Profile' }}
                </a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    ['icon' => 'leaf', 'title' => $t['home.card.spices'] ?? 'Spices', 'body' => $t['home.card.spices_body'] ?? 'Nutmeg, mace, pepper, cloves, chili, garlic, and other Indonesian origin products.'],
                    ['icon' => 'coffee', 'title' => $t['home.card.coffee'] ?? 'Coffee', 'body' => $t['home.card.coffee_body'] ?? 'Arabica and robusta green bean sourcing paths for local and international buyers.'],
                    ['icon' => 'truck', 'title' => $t['home.card.shipping'] ?? 'Shipping', 'body' => $t['home.card.shipping_body'] ?? 'Order follow-up, packing notes, destination needs, and shipment progress in one flow.'],
                    ['icon' => 'orders', 'title' => $t['home.card.invoice'] ?? 'Invoice', 'body' => $t['home.card.invoice_body'] ?? 'Member order history, payment status, and downloadable PDF invoice.'],
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
                    <p class="section-kicker text-amber-200"><x-icon name="package" class="h-4 w-4" />{{ $t['home.categories_kicker'] ?? 'Commodity Lines' }}</p>
                    <h2 class="mt-3 max-w-3xl text-4xl font-black sm:text-5xl">{{ $t['home.categories_title'] ?? 'Choose a category and move straight into sourcing.' }}</h2>
                </div>
                <a class="btn-secondary" href="{{ route('products.index') }}">{{ $t['home.all_products'] ?? 'All Products' }}</a>
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
                    <div class="border border-white/10 bg-white/10 p-6 text-slate-200">{{ $t['home.empty_categories'] ?? 'Categories will appear here after admin publishes them.' }}</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-pad bg-white">
        <div class="agape-container">
            <div class="grid gap-6 lg:grid-cols-[0.75fr_1.25fr]">
                <div data-reveal>
                    <p class="section-kicker"><x-icon name="coffee" class="h-4 w-4" />{{ $t['home.featured_kicker'] ?? 'Buyer Picks' }}</p>
                    <h2 class="mt-3 text-4xl font-black leading-tight text-slate-950 sm:text-5xl">{{ $t['home.featured_title'] ?? 'Popular products ready to request.' }}</h2>
                    <p class="mt-4 leading-8 text-slate-600">{{ $t['home.featured_body'] ?? 'See the product photo, category, minimum order, available stock, shipping readiness, and quote guidance in a compact buyer-friendly list.' }}</p>
                    <a class="btn-primary mt-6" href="{{ route('products.index') }}">{{ $t['home.full_catalog'] ?? 'Open Full Catalog' }}</a>
                </div>

                <div class="grid gap-3">
                    @forelse ($featuredProducts as $product)
                        <a class="market-row" href="{{ route('products.show', $product) }}" data-reveal>
                            <img class="h-20 w-20 shrink-0 object-cover" src="{{ $product->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $product->name }}" loading="lazy" decoding="async">
                            <div class="grid gap-3 md:grid-cols-[1fr_120px_120px_120px] md:items-center">
                                <div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="status-pill text-teal-700">{{ $product->category?->name ?? ($t['home.uncategorized'] ?? 'Uncategorized') }}</span>
                                        @if ($product->export_ready)
                                            <span class="status-pill border-amber-200 bg-amber-50 text-amber-800">{{ $t['home.shipping_ready'] ?? 'Shipping Ready' }}</span>
                                        @endif
                                    </div>
                                    <h3 class="mt-2 font-black text-slate-950">{{ $product->name }}</h3>
                                </div>
                                <div class="text-sm font-bold text-slate-600">{{ $t['home.moq'] ?? 'Min. Order' }}<br><span class="text-slate-950">{{ $product->formattedMoq() }}</span></div>
                                <div class="text-sm font-bold text-slate-600">{{ $t['home.stock'] ?? 'Stock' }}<br><span class="text-slate-950">{{ $product->formattedStock() }}</span></div>
                                <div class="text-sm font-black leading-snug text-teal-800">
                                    {{ $quoteLabel }}
                                    <span class="mt-1 block text-xs font-bold text-slate-500">{{ $quoteHint }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="trade-panel p-6 text-slate-600">{{ $t['home.empty_featured'] ?? 'Featured products will appear here after admin marks products as featured.' }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section id="shipping" class="section-pad bg-[#f4f6f3]">
        <div class="agape-container grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1" data-reveal>
                <p class="section-kicker"><x-icon name="truck" class="h-4 w-4" />{{ $t['home.shipping_kicker'] ?? 'Shipping Flow' }}</p>
                <h2 class="mt-3 text-4xl font-black text-slate-950">{{ $t['home.shipping_title'] ?? 'From inquiry to invoice.' }}</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-3 lg:col-span-2">
                @foreach ([
                    ['title' => $t['home.flow.requirements_title'] ?? 'Buyer sends requirements', 'body' => $t['home.flow.requirements_body'] ?? 'Product, grade, quantity, destination, packing needs, and timeline.'],
                    ['title' => $t['home.flow.followup_title'] ?? 'Admin follows up', 'body' => $t['home.flow.followup_body'] ?? 'Contact form and order data stay visible in admin for faster response.'],
                    ['title' => $t['home.flow.payment_title'] ?? 'Payment and invoice', 'body' => $t['home.flow.payment_body'] ?? 'Online payment popup, WhatsApp option, and PDF invoice for members.'],
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
                        <h2 class="mt-3 text-4xl font-black text-slate-950">{{ $t['home.gallery_title'] ?? 'Visual sourcing board.' }}</h2>
                    </div>
                </div>
                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($galleries as $gallery)
                        <figure class="trade-panel overflow-hidden" data-reveal>
                            <img class="h-60 w-full object-cover" src="{{ $gallery->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $gallery->title }}" loading="lazy" decoding="async">
                            <figcaption class="p-4 text-sm font-bold text-slate-800">{{ $gallery->title }}</figcaption>
                        </figure>
                    @empty
                        <div class="trade-panel p-6 text-slate-600 sm:col-span-2 lg:col-span-3">{{ $t['home.empty_gallery'] ?? 'Gallery items will appear after admin uploads them.' }}</div>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    @if ($siteAppearance['show_testimonials'] ?? true)
        <section class="section-pad bg-[#101820] text-white">
            <div class="agape-container">
                <p class="section-kicker text-amber-200"><x-icon name="message" class="h-4 w-4" />{{ $t['home.testimonials_kicker'] ?? 'Testimonials' }}</p>
                <div class="mt-6 grid gap-5 md:grid-cols-3">
                    @forelse ($testimonials as $testimonial)
                        <blockquote class="border border-white/10 bg-white/10 p-6" data-reveal>
                            <p class="text-sm leading-7 text-slate-100">"{{ $testimonial->message }}"</p>
                            <footer class="mt-5 font-black">{{ $testimonial->name }} <span class="font-medium text-slate-300">/ {{ $testimonial->country }}</span></footer>
                        </blockquote>
                    @empty
                        <div class="border border-white/10 bg-white/10 p-6 text-slate-200 md:col-span-3">{{ $t['home.empty_testimonials'] ?? 'Testimonials will appear after admin publishes them.' }}</div>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    <section id="faq" class="section-pad bg-white">
        <div class="agape-container grid gap-8 lg:grid-cols-[0.75fr_1.25fr]">
            <div data-reveal>
                <p class="section-kicker"><x-icon name="search" class="h-4 w-4" />{{ $t['home.faq_kicker'] ?? 'FAQ' }}</p>
                <h2 class="mt-3 text-4xl font-black text-slate-950">{{ $t['home.faq_title'] ?? 'Buyer questions, answered fast.' }}</h2>
            </div>
            <div class="grid gap-3">
                @forelse ($faqs as $faq)
                    <details class="border border-slate-200 bg-[#f8faf9] p-5 transition open:bg-white open:shadow-sm" data-reveal>
                        <summary class="cursor-pointer font-black text-slate-950">{{ $faq->question }}</summary>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $faq->answer }}</p>
                    </details>
                @empty
                    <div class="trade-panel p-6 text-slate-600">{{ $t['home.empty_faq'] ?? 'FAQ content will appear after admin publishes it.' }}</div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="contact" class="section-pad bg-[#f4f6f3]">
        <div class="agape-container grid gap-8 lg:grid-cols-[1fr_0.9fr]">
            <form class="trade-panel p-6 md:p-8" action="{{ route('contact.store') }}" method="POST" data-reveal>
                @csrf
                <div class="logo-stripe -mx-6 -mt-6 mb-6 md:-mx-8 md:-mt-8"><span></span><span></span><span></span></div>
                <p class="section-kicker"><x-icon name="message" class="h-4 w-4" />{{ $t['home.contact_kicker'] ?? 'Contact Form' }}</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950">{{ $t['home.contact_title'] ?? 'Send sourcing request.' }}</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <input class="field-input" name="name" value="{{ old('name') }}" placeholder="{{ $t['home.contact_name'] ?? 'Full name' }}" required>
                    <input class="field-input" type="email" name="email" value="{{ old('email') }}" placeholder="{{ $t['home.contact_email'] ?? 'Email address' }}" required>
                    <input class="field-input" name="phone" value="{{ old('phone') }}" placeholder="{{ $t['home.contact_phone'] ?? 'Phone / WhatsApp' }}">
                    <input class="field-input" name="company_name" value="{{ old('company_name') }}" placeholder="{{ $t['home.contact_company'] ?? 'Company name' }}">
                    <select class="field-input md:col-span-2" name="interest">
                        <option value="">{{ $t['home.contact_interest'] ?? 'Select interest' }}</option>
                        @foreach (['Spices', 'Arabica Coffee', 'Robusta Coffee', 'Shipping Partnership', 'Bulk Inquiry', 'Other'] as $interest)
                            <option value="{{ $interest }}" @selected(old('interest') === $interest)>{{ $interest }}</option>
                        @endforeach
                    </select>
                </div>
                <textarea class="field-input mt-4 min-h-36" name="message" placeholder="{{ $t['home.contact_message'] ?? 'Product, grade, quantity, destination, packing, timeline...' }}" required>{{ old('message') }}</textarea>
                <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <button class="btn-primary" type="submit"><x-icon name="message" class="h-4 w-4" />{{ $t['home.contact_submit'] ?? 'Send to Admin' }}</button>
                    <a class="btn-secondary" href="https://wa.me/{{ preg_replace('/\D+/', '', $siteContact['whatsapp']) }}?text={{ rawurlencode('Hello Agape153, I would like to ask about your product catalog.') }}" target="_blank" rel="noopener"><x-icon name="phone" class="h-4 w-4" />WhatsApp</a>
                </div>
            </form>

            <div data-reveal>
                <x-logo />
                <p class="section-kicker mt-7"><x-icon name="phone" class="h-4 w-4" />{{ $t['home.contact_desk'] ?? 'Contact Desk' }}</p>
                <h2 class="mt-3 text-4xl font-black text-slate-950">{{ $t['home.contact_heading'] ?? 'Talk to Agape153.' }}</h2>
                <div class="mt-6 grid gap-3">
                    <a class="trade-panel flex items-center gap-3 p-4" href="mailto:{{ $siteContact['email'] }}">
                        <x-icon name="mail" class="h-5 w-5 text-teal-700" />
                        <span class="grid gap-1">
                            <span class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">Primary Email</span>
                            <span class="break-words font-black text-slate-950">{{ $siteContact['email'] }}</span>
                        </span>
                    </a>
                    @if (! empty($siteContact['secondary_email']) && $siteContact['secondary_email'] !== $siteContact['email'])
                        <a class="trade-panel flex items-center gap-3 p-4" href="mailto:{{ $siteContact['secondary_email'] }}">
                            <x-icon name="mail" class="h-5 w-5 text-amber-600" />
                            <span class="grid gap-1">
                                <span class="text-xs font-black uppercase tracking-[0.12em] text-slate-500">Gmail Backup</span>
                                <span class="break-words font-black text-slate-950">{{ $siteContact['secondary_email'] }}</span>
                            </span>
                        </a>
                    @endif
                    <a class="trade-panel flex items-center gap-3 p-4" href="https://wa.me/{{ preg_replace('/\D+/', '', $siteContact['whatsapp']) }}" target="_blank" rel="noopener">
                        <x-icon name="phone" class="h-5 w-5 text-teal-700" />
                        <span class="font-black text-slate-950">{{ $siteContact['phone'] }}</span>
                    </a>
                </div>
                <div class="mt-6 flex flex-wrap gap-2">
                    <a class="icon-button" href="{{ $siteContact['youtube_url'] }}" target="_blank" rel="noopener" title="YouTube" aria-label="YouTube"><x-icon name="youtube" class="h-5 w-5" /></a>
                    <a class="icon-button" href="{{ $siteContact['instagram_url'] }}" target="_blank" rel="noopener" title="Instagram" aria-label="Instagram"><x-icon name="instagram" class="h-5 w-5" /></a>
                    <a class="icon-button" href="{{ $siteContact['linkedin_url'] }}" target="_blank" rel="noopener" title="LinkedIn" aria-label="LinkedIn"><x-icon name="linkedin" class="h-5 w-5" /></a>
                    <a class="icon-button" href="{{ $siteContact['tiktok_url'] }}" target="_blank" rel="noopener" title="TikTok" aria-label="TikTok"><x-icon name="tiktok" class="h-5 w-5" /></a>
                    <a class="icon-button" href="{{ $siteContact['threads_url'] }}" target="_blank" rel="noopener" title="Threads" aria-label="Threads"><x-icon name="threads" class="h-5 w-5" /></a>
                </div>
            </div>
        </div>
    </section>
@endsection
