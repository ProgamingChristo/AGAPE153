@extends('layouts.app')

@section('title', $product->displayName().' - Agape153')
@section('description', $product->displayShortDescription())

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Product",
    "name": "{{ $product->displayName() }}",
    "description": "{{ $product->displayShortDescription() }}",
    "image": "{{ $product->displayImageUrl() }}",
    "brand": {"@@type": "Brand", "name": "Agape153"}
}
</script>
@endsection

@section('content')
    @php
        $t = $siteText ?? [];
        $quoteLabel = $t['product.quote_label'] ?? 'Please contact or drop us email';
        $quoteHint = $t['product.quote_hint'] ?? 'Pricing depends on MOQ';
        $buyerDetails = $product->buyerDetails();
        $detailKey = fn ($label) => strtolower((string) preg_replace('/[^a-z0-9]+/i', '', (string) $label));
        $buyerDetailKeys = collect($buyerDetails)
            ->map(fn ($detail) => $detailKey($detail['label'] ?? ''))
            ->filter()
            ->all();
        $rawDetailKeys = collect($product->product_details ?? [])
            ->map(fn ($detail) => $detailKey($detail['label'] ?? ''))
            ->filter()
            ->all();
        $hiddenDetailKeys = array_unique([...$buyerDetailKeys, ...$rawDetailKeys, 'productname', 'itemname']);
    @endphp
    <section class="bg-[#101820] text-white">
        <div class="logo-stripe"><span></span><span></span><span></span></div>
        <div class="agape-container grid gap-8 py-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
            <div data-reveal>
                <div class="flex flex-wrap gap-2">
                    @if ($product->category)
                        <a class="status-pill bg-white text-teal-700" href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->displayName() }}</a>
                    @else
                        <span class="status-pill bg-white text-teal-700">{{ $t['home.uncategorized'] ?? 'Uncategorized' }}</span>
                    @endif
                    @if ($product->export_ready)
                        <span class="status-pill border-amber-200 bg-amber-50 text-amber-800">{{ $t['home.shipping_ready'] ?? 'Shipping Ready' }}</span>
                    @endif
                    <span class="status-pill bg-white">{{ $product->stock_quantity > 0 ? ($t['product.in_stock'] ?? 'In Stock') : ($t['product.preorder'] ?? 'Preorder') }}</span>
                </div>
                <h1 class="mt-4 text-5xl font-black leading-tight text-white sm:text-7xl">{{ $product->displayName() }}</h1>
                <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-200">{{ $product->displayShortDescription() }}</p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4" data-reveal>
                <div class="border border-white/10 bg-white/10 p-4">
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-300">{{ $t['product.quote'] ?? 'Quote' }}</div>
                    <div class="mt-2 text-lg font-black leading-tight text-amber-200">{{ $quoteLabel }}</div>
                    <div class="mt-1 text-xs font-bold text-slate-300">{{ $quoteHint }}</div>
                </div>
                <div class="border border-white/10 bg-white/10 p-4">
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-300">MOQ</div>
                    <div class="mt-2 text-xl font-black text-white">{{ $product->formattedMoq() }}</div>
                </div>
                <div class="border border-white/10 bg-white/10 p-4">
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-300">{{ $t['home.stock'] ?? 'Stock' }}</div>
                    <div class="mt-2 text-xl font-black text-white">{{ $product->formattedStock() }}</div>
                </div>
                <div class="border border-white/10 bg-white/10 p-4">
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-300">{{ $t['product.origin'] ?? 'Origin' }}</div>
                    <div class="mt-2 text-xl font-black text-white">{{ $product->origin ?: 'ID' }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#f4f6f3]">
        <div class="agape-container grid gap-8 lg:grid-cols-[1fr_420px]">
            <div data-reveal>
                <div class="trade-panel overflow-hidden">
                    <img class="aspect-square w-full object-cover sm:aspect-[4/3]" src="{{ $product->displayImageUrl() ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $product->displayName() }}" loading="eager" decoding="async">
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
                            {{ $t['product.product_video'] ?? 'Product Video' }}
                        </div>
                        @if ($videoFileUrl)
                            <video class="aspect-video w-full bg-slate-950 object-cover" src="{{ $videoFileUrl }}" poster="{{ $product->displayImageUrl() ?: asset('images/product-placeholder.svg') }}" controls preload="metadata"></video>
                        @elseif ($videoEmbedUrl)
                            <iframe class="aspect-video w-full bg-slate-950" src="{{ $videoEmbedUrl }}" title="{{ $product->displayName() }} video" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        @else
                            <div class="grid aspect-video place-items-center p-6 text-center text-white">
                                <a class="btn-secondary" href="{{ $product->video_url }}" target="_blank" rel="noopener">
                                    <x-icon name="video" class="h-4 w-4" />
                                    {{ $t['product.open_video'] ?? 'Open Product Video' }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
                @if ($product->images->isNotEmpty())
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ($product->images as $image)
                            <img class="aspect-square border border-slate-200 object-cover" src="{{ $image->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $image->alt_text ?: $product->displayName() }}" loading="lazy" decoding="async">
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="h-max trade-panel p-5" data-reveal>
                <p class="section-kicker"><x-icon name="orders" class="h-4 w-4" />{{ $t['product.details'] ?? 'Product Details' }}</p>
                <dl class="mt-5 grid gap-3 text-sm">
                    @foreach ($buyerDetails as $detail)
                        <div class="grid gap-1 border-b border-slate-100 pb-3 last:border-b-0 last:pb-0">
                            <dt class="text-xs font-black uppercase tracking-[0.12em] text-teal-700">{{ $detail['label'] }}</dt>
                            <dd class="break-words font-black leading-6 text-slate-950">{{ $detail['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                @auth
                    <form class="mt-6 grid gap-3" action="{{ route('cart.store', $product) }}" method="POST">
                        @csrf
                        <input class="field-input" type="number" name="quantity" value="{{ $product->min_order_quantity }}" min="1">
                        <button class="btn-primary w-full" type="submit">
                            <x-icon name="cart" class="h-4 w-4" />
                            {{ $t['product.add_to_cart'] ?? 'Add to Cart' }}
                        </button>
                        <button class="btn-secondary w-full" formaction="{{ route('wishlist.toggle', $product) }}" formmethod="POST">
                            <x-icon name="check" class="h-4 w-4" />
                            {{ $t['product.wishlist'] ?? 'Wishlist' }}
                        </button>
                    </form>
                @else
                    <div class="mt-6 border border-teal-200 bg-teal-50 p-4">
                        <div class="font-black text-slate-950">{{ $t['product.login_required_title'] ?? 'Login required for cart' }}</div>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $t['product.login_required_body'] ?? 'Please log in before adding this product to your cart.' }}</p>
                        <a class="btn-primary mt-4 w-full" href="{{ route('login') }}">
                            <x-icon name="login" class="h-4 w-4" />
                            {{ $t['product.login_to_cart'] ?? 'Login to Add to Cart' }}
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
                ->reject(fn ($detail) => in_array($detailKey($detail['label'] ?? ''), $hiddenDetailKeys, true))
                ->values();
            $publishedReviews = $product->publishedReviews;
            $averageRating = $publishedReviews->avg('rating');
        @endphp
        <div class="agape-container grid gap-8 lg:grid-cols-[0.7fr_1.3fr]">
            <div data-reveal>
                <p class="section-kicker"><x-icon name="leaf" class="h-4 w-4" />{{ $t['product.specification'] ?? 'Specifications' }}</p>
                <h2 class="mt-3 text-4xl font-black text-slate-950">{{ $t['product.detail_heading'] ?? 'Product details.' }}</h2>
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

                @if ($product->displayDescription())
                    <div class="{{ $productDetails->isNotEmpty() ? 'mt-6 border-t border-slate-200 pt-6' : '' }} leading-8 text-slate-600">
                        {!! nl2br(e($product->displayDescription())) !!}
                    </div>
                @elseif ($productDetails->isEmpty())
                    <div class="leading-8 text-slate-600">
                        {{ $t['product.detail_preparing'] ?? 'Agape153 is preparing the product details.' }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="section-pad bg-[#f4f6f3]">
        <div class="agape-container">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end" data-reveal>
                <div>
                    <p class="section-kicker"><x-icon name="star" class="h-4 w-4" />{{ $t['product.reviews_kicker'] ?? 'Customer Reviews' }}</p>
                    <h2 class="mt-3 text-4xl font-black text-slate-950">{{ $t['product.reviews_heading'] ?? 'Product rating.' }}</h2>
                </div>
                @if ($publishedReviews->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 text-right shadow-sm">
                        <div class="text-3xl font-black text-amber-500">{{ number_format((float) $averageRating, 1) }}/5</div>
                        <div class="mt-1 text-sm font-bold text-slate-500">{{ $publishedReviews->count() }} {{ $publishedReviews->count() === 1 ? ($t['product.review_singular'] ?? 'review') : ($t['product.review_plural'] ?? 'reviews') }}</div>
                    </div>
                @endif
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @forelse ($publishedReviews as $review)
                    <article class="trade-panel p-5" data-reveal>
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="font-black text-slate-950">{{ $review->user?->name ?? ($t['product.verified_buyer'] ?? 'Verified Buyer') }}</div>
                                <div class="mt-1 text-xs font-bold text-slate-400">{{ $review->created_at->format('d M Y') }}</div>
                            </div>
                            <div class="flex items-center gap-1 text-amber-500">
                                @for ($i = 1; $i <= 5; $i++)
                                    <x-icon name="star" class="h-4 w-4 {{ $i <= $review->rating ? 'text-amber-500' : 'text-slate-300' }}" />
                                @endfor
                            </div>
                        </div>
                        <p class="mt-4 text-sm leading-7 text-slate-600">{{ $review->comment ?: ($t['product.no_comment'] ?? 'No comment.') }}</p>
                        @if ($review->admin_reply)
                            <div class="mt-4 rounded-2xl border border-teal-200 bg-teal-50 p-4 text-sm leading-6 text-teal-900">
                                <div class="font-black">{{ $t['product.admin_reply'] ?? 'Agape153 reply' }}</div>
                                <p class="mt-1">{{ $review->admin_reply }}</p>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="trade-panel p-6 text-slate-600" data-reveal>
                        {{ $t['product.empty_reviews'] ?? 'There are no reviews for this product yet.' }}
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="section-pad bg-[#f4f6f3]">
            <div class="agape-container">
                <p class="section-kicker"><x-icon name="package" class="h-4 w-4" />{{ $t['product.related'] ?? 'Related Products' }}</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($relatedProducts as $related)
                        <a class="market-row" href="{{ route('products.show', $related) }}" data-reveal>
                            <img class="h-20 w-20 shrink-0 object-cover" src="{{ $related->displayImageUrl() ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $related->displayName() }}" loading="lazy" decoding="async">
                            <div>
                                <div class="font-black text-slate-950">{{ $related->displayName() }}</div>
                                <div class="mt-2 text-sm font-black text-teal-800">{{ $quoteLabel }}</div>
                                <div class="mt-1 text-xs font-bold text-slate-500">{{ $quoteHint }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
