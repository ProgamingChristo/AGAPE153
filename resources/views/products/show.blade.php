@extends('layouts.app')

@section('title', ($product->meta_title ?: $product->name).' - Agape153')
@section('description', $product->meta_description ?: $product->short_description)

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Product",
    "name": "{{ $product->name }}",
    "description": "{{ $product->short_description }}",
    "image": "{{ $product->image_url }}",
    "brand": {"@@type": "Brand", "name": "Agape153"},
    "offers": {
        "@@type": "Offer",
        "priceCurrency": "{{ $product->currency }}",
        "price": "{{ $product->price ?? 0 }}",
        "availability": "{{ $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/PreOrder' }}"
    }
}
</script>
@endsection

@section('content')
    <section class="bg-[#101820] text-white">
        <div class="logo-stripe"><span></span><span></span><span></span></div>
        <div class="agape-container grid gap-8 py-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
            <div data-reveal>
                <div class="flex flex-wrap gap-2">
                    <a class="status-pill bg-white text-teal-700" href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                    @if ($product->export_ready)
                        <span class="status-pill border-amber-200 bg-amber-50 text-amber-800">Export Ready</span>
                    @endif
                    <span class="status-pill bg-white">{{ $product->stock_quantity > 0 ? 'In Stock' : 'Preorder' }}</span>
                </div>
                <h1 class="mt-4 text-5xl font-black leading-tight text-white sm:text-7xl">{{ $product->name }}</h1>
                <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-200">{{ $product->short_description }}</p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4" data-reveal>
                <div class="border border-white/10 bg-white/10 p-4">
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-300">Price</div>
                    <div class="mt-2 text-xl font-black text-amber-200">{{ $product->formattedPrice() }}</div>
                </div>
                <div class="border border-white/10 bg-white/10 p-4">
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-300">MOQ</div>
                    <div class="mt-2 text-xl font-black text-white">{{ $product->min_order_quantity }} {{ $product->unit }}</div>
                </div>
                <div class="border border-white/10 bg-white/10 p-4">
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-300">Stock</div>
                    <div class="mt-2 text-xl font-black text-white">{{ $product->stock_quantity }}</div>
                </div>
                <div class="border border-white/10 bg-white/10 p-4">
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-300">Origin</div>
                    <div class="mt-2 text-xl font-black text-white">{{ $product->origin ?: 'ID' }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#f4f6f3]">
        <div class="agape-container grid gap-8 lg:grid-cols-[1fr_420px]">
            <div data-reveal>
                <div class="trade-panel overflow-hidden">
                    <img class="aspect-square w-full object-cover sm:aspect-[4/3]" src="{{ $product->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $product->name }}" loading="eager" decoding="async">
                    <div class="logo-stripe"><span></span><span></span><span></span></div>
                </div>
                @if ($product->video_url)
                    @php
                        $videoEmbedUrl = $product->videoEmbedUrl();
                        $videoFileUrl = $product->videoFileUrl();
                    @endphp
                    <div class="trade-panel mt-4 overflow-hidden bg-slate-950">
                        <div class="flex items-center gap-2 border-b border-white/10 px-4 py-3 text-sm font-black text-white">
                            <x-icon name="video" class="h-4 w-4 text-amber-200" />
                            Product Video
                        </div>
                        @if ($videoFileUrl)
                            <video class="aspect-video w-full bg-slate-950 object-cover" src="{{ $videoFileUrl }}" poster="{{ $product->image_url ?: asset('images/product-placeholder.svg') }}" controls preload="metadata"></video>
                        @elseif ($videoEmbedUrl)
                            <iframe class="aspect-video w-full bg-slate-950" src="{{ $videoEmbedUrl }}" title="{{ $product->name }} video" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        @else
                            <div class="grid aspect-video place-items-center p-6 text-center text-white">
                                <a class="btn-secondary" href="{{ $product->video_url }}" target="_blank" rel="noopener">
                                    <x-icon name="video" class="h-4 w-4" />
                                    Open Product Video
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
                @if ($product->images->isNotEmpty())
                    <div class="mt-4 grid grid-cols-4 gap-3">
                        @foreach ($product->images as $image)
                            <img class="aspect-square border border-slate-200 object-cover" src="{{ $image->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $image->alt_text ?: $product->name }}" loading="lazy" decoding="async">
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="h-max trade-panel p-5" data-reveal>
                <p class="section-kicker"><x-icon name="orders" class="h-4 w-4" />Product Sheet</p>
                <dl class="mt-5 grid gap-3 text-sm">
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <dt class="text-slate-500">Grade</dt>
                        <dd class="font-black text-slate-950">{{ $product->grade ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <dt class="text-slate-500">Export Ready</dt>
                        <dd class="font-black text-slate-950">{{ $product->export_ready ? 'Yes' : 'Inquiry required' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <dt class="text-slate-500">Unit</dt>
                        <dd class="font-black text-slate-950">{{ $product->unit }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Currency</dt>
                        <dd class="font-black text-slate-950">{{ $product->currency }}</dd>
                    </div>
                </dl>

                @auth
                    <form class="mt-6 grid gap-3" action="{{ route('cart.store', $product) }}" method="POST">
                        @csrf
                        <input class="field-input" type="number" name="quantity" value="{{ $product->min_order_quantity }}" min="1">
                        <button class="btn-primary w-full" type="submit">
                            <x-icon name="cart" class="h-4 w-4" />
                            Add to Cart
                        </button>
                        <button class="btn-secondary w-full" formaction="{{ route('wishlist.toggle', $product) }}" formmethod="POST">
                            <x-icon name="check" class="h-4 w-4" />
                            Wishlist
                        </button>
                    </form>
                @else
                    <div class="mt-6 border border-teal-200 bg-teal-50 p-4">
                        <div class="font-black text-slate-950">Login required for cart</div>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Silakan login terlebih dahulu untuk menambahkan produk ke keranjang.</p>
                        <a class="btn-primary mt-4 w-full" href="{{ route('login') }}">
                            <x-icon name="login" class="h-4 w-4" />
                            Login to Add Cart
                        </a>
                    </div>
                @endauth
            </aside>
        </div>
    </section>

    <section class="bg-white py-10">
        @php
            $productDetails = collect($product->product_details ?? [])
                ->filter(fn ($detail) => filled($detail['value'] ?? null))
                ->values();
            $publishedReviews = $product->publishedReviews;
            $averageRating = $publishedReviews->avg('rating');
        @endphp
        <div class="agape-container grid gap-8 lg:grid-cols-[0.7fr_1.3fr]">
            <div data-reveal>
                <p class="section-kicker"><x-icon name="leaf" class="h-4 w-4" />Specification</p>
                <h2 class="mt-3 text-4xl font-black text-slate-950">Product details.</h2>
            </div>
            <div class="trade-panel p-6" data-reveal>
                @if ($productDetails->isNotEmpty())
                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach ($productDetails as $detail)
                            <div class="border border-slate-200 bg-[#f8faf9] p-4">
                                <div class="text-xs font-black uppercase tracking-[0.14em] text-teal-700">{{ $detail['label'] ?? 'Detail' }}</div>
                                <div class="mt-2 text-base font-bold leading-7 text-slate-950">{{ $detail['value'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($product->description || $product->short_description)
                    <div class="{{ $productDetails->isNotEmpty() ? 'mt-6 border-t border-slate-200 pt-6' : '' }} leading-8 text-slate-600">
                        {!! nl2br(e($product->description ?: $product->short_description)) !!}
                    </div>
                @elseif ($productDetails->isEmpty())
                    <div class="leading-8 text-slate-600">
                        Product detail is being prepared by Agape153.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#f4f6f3]">
        <div class="agape-container">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end" data-reveal>
                <div>
                    <p class="section-kicker"><x-icon name="star" class="h-4 w-4" />Customer Reviews</p>
                    <h2 class="mt-3 text-4xl font-black text-slate-950">Product rating.</h2>
                </div>
                @if ($publishedReviews->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 text-right shadow-sm">
                        <div class="text-3xl font-black text-amber-500">{{ number_format((float) $averageRating, 1) }}/5</div>
                        <div class="mt-1 text-sm font-bold text-slate-500">{{ $publishedReviews->count() }} review</div>
                    </div>
                @endif
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @forelse ($publishedReviews as $review)
                    <article class="trade-panel p-5" data-reveal>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="font-black text-slate-950">{{ $review->user?->name ?? 'Verified Buyer' }}</div>
                                <div class="mt-1 text-xs font-bold text-slate-400">{{ $review->created_at->format('d M Y') }}</div>
                            </div>
                            <div class="flex items-center gap-1 text-amber-500">
                                @for ($i = 1; $i <= 5; $i++)
                                    <x-icon name="star" class="h-4 w-4 {{ $i <= $review->rating ? 'text-amber-500' : 'text-slate-300' }}" />
                                @endfor
                            </div>
                        </div>
                        <p class="mt-4 text-sm leading-7 text-slate-600">{{ $review->comment ?: 'No comment.' }}</p>
                        @if ($review->admin_reply)
                            <div class="mt-4 rounded-2xl border border-teal-200 bg-teal-50 p-4 text-sm leading-6 text-teal-900">
                                <div class="font-black">Agape153 reply</div>
                                <p class="mt-1">{{ $review->admin_reply }}</p>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="trade-panel p-6 text-slate-600" data-reveal>
                        Belum ada review untuk produk ini.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="section-pad bg-[#f4f6f3]">
            <div class="agape-container">
                <p class="section-kicker"><x-icon name="package" class="h-4 w-4" />Related Products</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($relatedProducts as $related)
                        <a class="market-row" href="{{ route('products.show', $related) }}" data-reveal>
                            <img class="h-20 w-20 shrink-0 object-cover" src="{{ $related->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $related->name }}" loading="lazy" decoding="async">
                            <div>
                                <div class="font-black text-slate-950">{{ $related->name }}</div>
                                <div class="mt-2 text-sm font-bold text-teal-800">{{ $related->formattedPrice() }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
