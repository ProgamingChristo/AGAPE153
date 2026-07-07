@extends('layouts.app')

@section('title', 'Agape153 - Indonesian Spices & Coffee Export')
@section('description', 'Supplier rempah-rempah dan kopi Indonesia untuk kebutuhan lokal dan ekspor internasional.')

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "Agape153",
    "url": "{{ url('/') }}",
    "sameAs": [],
    "contactPoint": {
        "@@type": "ContactPoint",
        "telephone": "+{{ $whatsappNumber }}",
        "contactType": "sales"
    }
}
</script>
@endsection

@section('content')
    <section class="relative overflow-hidden bg-[#edf7f4]">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-35" src="https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=1800&q=80" alt="Indonesian spices">
        </div>
        <div class="agape-container relative grid min-h-[calc(100vh-4rem)] items-center gap-10 py-12 lg:grid-cols-[1.1fr_0.9fr]">
            <div>
                <div class="inline-flex rounded-full border border-teal-200 bg-white/80 px-4 py-2 text-sm font-bold text-teal-800">Indonesian spices and coffee supplier</div>
                <h1 class="mt-6 max-w-4xl text-5xl font-black leading-tight text-slate-950 sm:text-6xl lg:text-7xl">Agape153</h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-700">Katalog rempah-rempah, kopi arabica, dan robusta Indonesia untuk pembeli lokal, retail, horeca, distributor, dan importir internasional.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="btn-primary">Browse Products</a>
                    <a href="#contact" class="btn-secondary">Become Partner</a>
                </div>
                <dl class="mt-10 grid max-w-2xl grid-cols-3 gap-4">
                    <div>
                        <dt class="text-3xl font-black text-teal-800">{{ $featuredProducts->count() }}+</dt>
                        <dd class="text-sm text-slate-600">Featured products</dd>
                    </div>
                    <div>
                        <dt class="text-3xl font-black text-teal-800">Global</dt>
                        <dd class="text-sm text-slate-600">Export ready</dd>
                    </div>
                    <div>
                        <dt class="text-3xl font-black text-teal-800">IDR</dt>
                        <dd class="text-sm text-slate-600">Dynamic pricing</dd>
                    </div>
                </dl>
            </div>
            <div class="soft-panel rounded-2xl p-4">
                <img class="aspect-[4/5] w-full rounded-xl object-cover" src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=900&q=80" alt="Indonesian coffee">
            </div>
        </div>
    </section>

    <section id="about" class="section-pad bg-white">
        <div class="agape-container grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Company Profile</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950 sm:text-4xl">Built for trust, repeat orders, and international buying flow.</h2>
            </div>
            <div class="grid gap-5 text-slate-650 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-6">
                    <h3 class="font-black text-slate-950">Vision</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Menjadi pemasok rempah dan kopi Indonesia yang dipercaya di pasar lokal dan global.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-6">
                    <h3 class="font-black text-slate-950">Mission</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Menghubungkan produk terbaik Indonesia dengan pembeli melalui katalog digital, proses order yang jelas, dan komunikasi cepat.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad bg-slate-950 text-white">
        <div class="agape-container">
            <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-300">Categories</p>
                    <h2 class="mt-3 text-3xl font-black sm:text-4xl">Rempah, arabica, robusta, dan produk ekspor.</h2>
                </div>
                <a class="btn-secondary text-slate-950" href="{{ route('products.index') }}">All Products</a>
            </div>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group overflow-hidden rounded-2xl bg-white text-slate-950">
                        <img class="h-40 w-full object-cover transition duration-300 group-hover:scale-105" src="{{ $category->image_url }}" alt="{{ $category->name }}">
                        <div class="p-5">
                            <div class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">{{ $category->type }}</div>
                            <h3 class="mt-2 text-lg font-black">{{ $category->name }}</h3>
                            <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $category->description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#f8faf9]">
        <div class="agape-container">
            <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Featured Catalog</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-950 sm:text-4xl">Produk unggulan siap dipesan.</h2>
                </div>
                <a href="{{ route('products.index') }}" class="btn-primary">View Catalog</a>
            </div>
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredProducts as $product)
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <img class="h-56 w-full object-cover" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        <div class="p-6">
                            <div class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">{{ $product->category->name }}</div>
                            <h3 class="mt-2 text-xl font-black text-slate-950">{{ $product->name }}</h3>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $product->short_description }}</p>
                            <div class="mt-5 flex items-center justify-between gap-4">
                                <span class="font-black text-teal-800">{{ $product->formattedPrice() }}</span>
                                <a class="text-sm font-black text-slate-950 hover:text-teal-700" href="{{ route('products.show', $product) }}">Detail</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="export" class="section-pad bg-white">
        <div class="agape-container grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Export Products</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950">Made in Indonesia for global buyers.</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-3 lg:col-span-2">
                @foreach (['Curated origin and grade', 'WhatsApp inquiry workflow', 'Prepared for multilingual growth'] as $item)
                    <div class="rounded-2xl border border-slate-200 p-6">
                        <h3 class="font-black text-slate-950">{{ $item }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Proses dibuat ringkas untuk membantu tim sales merespons permintaan harga, MOQ, dan pengiriman.</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="certifications" class="section-pad bg-[#edf7f4]">
        <div class="agape-container grid gap-8 lg:grid-cols-2">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Why Choose Us</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950">Katalog profesional, harga dinamis, dan fondasi admin yang siap berkembang.</h2>
            </div>
            <div class="grid gap-4">
                @foreach (['Product catalog with search and filter', 'Manual WhatsApp checkout for fast sales follow-up', 'SEO-ready structure with sitemap and JSON-LD', 'Admin dashboard for product and category management'] as $item)
                    <div class="rounded-2xl bg-white p-5 font-bold text-slate-800 shadow-sm">{{ $item }}</div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad bg-white">
        <div class="agape-container">
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Gallery</p>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($galleries as $gallery)
                    <figure class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <img class="h-56 w-full object-cover" src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}">
                        <figcaption class="p-4 text-sm font-bold text-slate-800">{{ $gallery->title }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-pad bg-slate-950 text-white">
        <div class="agape-container">
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-300">Testimonials</p>
            <div class="mt-6 grid gap-5 md:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    <blockquote class="rounded-2xl bg-white/10 p-6">
                        <p class="text-sm leading-7 text-slate-100">"{{ $testimonial->message }}"</p>
                        <footer class="mt-5 font-black">{{ $testimonial->name }} <span class="font-medium text-slate-300">/ {{ $testimonial->country }}</span></footer>
                    </blockquote>
                @endforeach
            </div>
        </div>
    </section>

    <section id="faq" class="section-pad bg-white">
        <div class="agape-container grid gap-8 lg:grid-cols-[0.8fr_1.2fr]">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">FAQ</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950">Pertanyaan umum pembeli.</h2>
            </div>
            <div class="grid gap-3">
                @foreach ($faqs as $faq)
                    <details class="rounded-2xl border border-slate-200 p-5">
                        <summary class="cursor-pointer font-black text-slate-950">{{ $faq->question }}</summary>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $faq->answer }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    <section id="contact" class="section-pad bg-[#edf7f4]">
        <div class="agape-container grid gap-8 lg:grid-cols-2">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Contact</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950">Siap menjadi partner Agape153?</h2>
                <p class="mt-4 text-slate-600">Kirim kebutuhan produk, grade, kuantitas, negara tujuan, dan timeline pembelian. Tim sales dapat melanjutkan detail melalui WhatsApp.</p>
            </div>
            <div class="soft-panel rounded-2xl p-6">
                <a class="btn-primary w-full" href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsappNumber) }}?text={{ rawurlencode('Halo Agape153, saya ingin bertanya tentang katalog produk.') }}" target="_blank" rel="noopener">Contact via WhatsApp</a>
                <a class="btn-secondary mt-3 w-full" href="{{ route('products.index') }}">Download Catalog</a>
            </div>
        </div>
    </section>
@endsection
