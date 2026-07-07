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
        "availability": "https://schema.org/InStock"
    }
}
</script>
@endsection

@section('content')
    <section class="bg-white py-12">
        <div class="agape-container grid gap-10 lg:grid-cols-[0.95fr_1.05fr]">
            <div>
                <img class="aspect-[4/3] w-full rounded-2xl object-cover" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                @if ($product->images->isNotEmpty())
                    <div class="mt-4 grid grid-cols-4 gap-3">
                        @foreach ($product->images as $image)
                            <img class="aspect-square rounded-xl object-cover" src="{{ $image->image_url }}" alt="{{ $image->alt_text ?: $product->name }}">
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <a class="text-sm font-black uppercase tracking-[0.18em] text-teal-700" href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                <h1 class="mt-3 text-4xl font-black leading-tight text-slate-950">{{ $product->name }}</h1>
                <p class="mt-4 text-lg leading-8 text-slate-600">{{ $product->short_description }}</p>
                <div class="mt-6 grid gap-3 rounded-2xl border border-slate-200 bg-[#f8faf9] p-5 sm:grid-cols-2">
                    <div>
                        <div class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Price</div>
                        <div class="mt-1 text-2xl font-black text-teal-800">{{ $product->formattedPrice() }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Origin / Grade</div>
                        <div class="mt-1 font-bold text-slate-800">{{ $product->origin ?: 'Indonesia' }} {{ $product->grade ? '/ '.$product->grade : '' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">MOQ</div>
                        <div class="mt-1 font-bold text-slate-800">{{ $product->min_order_quantity }} {{ $product->unit }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Export Ready</div>
                        <div class="mt-1 font-bold text-slate-800">{{ $product->export_ready ? 'Yes' : 'Inquiry required' }}</div>
                    </div>
                </div>

                <form class="mt-6 flex flex-col gap-3 sm:flex-row" action="{{ route('cart.store', $product) }}" method="POST">
                    @csrf
                    <input class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-teal-500 sm:w-32" type="number" name="quantity" value="{{ $product->min_order_quantity }}" min="1">
                    <button class="btn-primary" type="submit">Add to Cart</button>
                    @auth
                        <button class="btn-secondary" formaction="{{ route('wishlist.toggle', $product) }}" formmethod="POST">Wishlist</button>
                    @endauth
                </form>

                <div class="prose prose-slate mt-8 max-w-none">
                    <h2>Product Detail</h2>
                    <p>{!! nl2br(e($product->description)) !!}</p>
                </div>
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="section-pad bg-[#f8faf9]">
            <div class="agape-container">
                <h2 class="text-3xl font-black text-slate-950">Related products</h2>
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($relatedProducts as $related)
                        <a class="overflow-hidden rounded-2xl border border-slate-200 bg-white" href="{{ route('products.show', $related) }}">
                            <img class="h-40 w-full object-cover" src="{{ $related->image_url }}" alt="{{ $related->name }}">
                            <div class="p-4">
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
