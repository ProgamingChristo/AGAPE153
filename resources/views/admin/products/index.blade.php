@extends('layouts.admin')

@section('title', 'Products - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Products</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Manage catalog.</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="btn-secondary" href="{{ route('admin.products.export') }}"><x-icon name="download" class="h-4 w-4" />Export CSV</a>
            <a class="btn-secondary" href="{{ route('admin.products.trash') }}">Trash</a>
            <a class="btn-primary" href="{{ route('admin.products.create') }}">Add Product</a>
        </div>
    </div>

    <form class="mt-6 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-center" method="POST" enctype="multipart/form-data" action="{{ route('admin.products.import') }}">
        @csrf
        <input class="rounded-xl border border-slate-200 px-4 py-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-teal-700 file:px-3 file:py-2 file:text-sm file:font-bold file:text-white" type="file" name="csv_file" accept=".csv,text/csv" required>
        <button class="btn-secondary" type="submit"><x-icon name="upload" class="h-4 w-4" />Import CSV</button>
    </form>

    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="logo-stripe"><span></span><span></span><span></span></div>
            <div class="p-5">
                <div class="text-sm font-bold text-slate-500">Total Products</div>
                <div class="mt-2 text-3xl font-black text-slate-950">{{ number_format($products->total()) }}</div>
            </div>
        </div>
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="h-1.5 bg-teal-600"></div>
            <div class="p-5">
                <div class="text-sm font-bold text-slate-500">Active Products</div>
                <div class="mt-2 text-3xl font-black text-teal-800">{{ number_format($activeProducts) }}</div>
            </div>
        </div>
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="h-1.5 bg-amber-400"></div>
            <div class="p-5">
                <div class="text-sm font-bold text-slate-500">Low Stock</div>
                <div class="mt-2 text-3xl font-black text-amber-700">{{ number_format($lowStockProducts) }}</div>
            </div>
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="hidden border-b border-slate-100 bg-slate-50 px-5 py-3 text-sm font-black text-slate-600 2xl:grid 2xl:grid-cols-[minmax(300px,1.3fr)_180px_150px_minmax(360px,0.9fr)_230px]">
            <span>Product</span>
            <span>Category</span>
            <span>Price</span>
            <span>Stock</span>
            <span>Actions</span>
        </div>
        @forelse ($products as $product)
            <div class="grid gap-5 border-b border-slate-100 p-5 text-sm 2xl:grid-cols-[minmax(300px,1.3fr)_180px_150px_minmax(360px,0.9fr)_230px] 2xl:items-center">
                <div class="flex min-w-0 items-start gap-4">
                    <img class="h-20 w-20 shrink-0 rounded-2xl object-cover shadow-sm" src="{{ $product->image_url ?: asset('images/product-placeholder.svg') }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $product->name }}">
                    <div class="min-w-0">
                        <div class="break-words text-base font-black text-slate-950">{{ $product->name }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ $product->sku ?: 'No SKU' }} - {{ count($product->product_details ?? []) }} details</div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $product->is_active ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-500' }}">
                                {{ $product->is_active ? 'ACTIVE' : 'INACTIVE' }}
                            </span>
                            @if ($product->export_ready)
                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-800">EXPORT</span>
                            @endif
                            @if ($product->video_url)
                                <span class="inline-flex items-center gap-1 rounded-full bg-sky-100 px-3 py-1 text-xs font-black text-sky-800">
                                    <x-icon name="video" class="h-3.5 w-3.5" />
                                    VIDEO
                                </span>
                            @endif
                            @if ($product->stock_quantity <= 5)
                                <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-black text-red-700">LOW STOCK</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-400 2xl:hidden">Category</div>
                    <div class="mt-1 font-bold text-slate-800">{{ $product->category?->name }}</div>
                </div>

                <div>
                    <div class="text-xs font-black uppercase tracking-[0.14em] text-slate-400 2xl:hidden">Price</div>
                    <div class="mt-1 font-black text-teal-800">{{ $product->formattedPrice() }}</div>
                </div>

                <form class="grid gap-2 rounded-2xl border border-slate-200 bg-[#f8faf9] p-3 sm:grid-cols-[120px_1fr_auto]" method="POST" action="{{ route('admin.products.stock', $product) }}">
                    @csrf
                    @method('PATCH')
                    <label class="grid gap-1 text-xs font-black uppercase tracking-[0.12em] text-slate-500">
                        Stock
                        <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 font-bold normal-case tracking-normal outline-none focus:border-teal-500" type="number" name="stock_quantity" min="0" value="{{ $product->stock_quantity }}" aria-label="Stock {{ $product->name }}">
                    </label>
                    <label class="grid gap-1 text-xs font-black uppercase tracking-[0.12em] text-slate-500">
                        Reason
                        <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 font-normal normal-case tracking-normal outline-none focus:border-teal-500" name="reason" placeholder="Cycle count / restock">
                    </label>
                    <button class="btn-secondary self-end px-4 py-2 text-sm" type="submit">Save</button>
                </form>

                <div class="flex flex-wrap gap-2 2xl:justify-end">
                    @if ($product->is_active)
                        <a class="btn-secondary px-3 py-2 text-sm" href="{{ route('products.show', $product) }}" target="_blank" rel="noopener">View</a>
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-2 text-sm font-black text-slate-400">Hidden</span>
                    @endif
                    <a class="btn-secondary px-3 py-2 text-sm text-teal-700" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.products.toggle-active', $product) }}">
                        @csrf
                        @method('PATCH')
                        <button class="btn-secondary px-3 py-2 text-sm {{ $product->is_active ? 'text-amber-700' : 'text-teal-700' }}" type="submit">
                            {{ $product->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn-secondary px-3 py-2 text-sm text-red-700" type="submit">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-slate-600">Belum ada produk.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $products->links() }}</div>

    <section class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col justify-between gap-3 md:flex-row md:items-end">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Customer Reviews</p>
                <h2 class="mt-2 text-3xl font-black text-slate-950">Latest product feedback.</h2>
            </div>
            <span class="status-pill">{{ $recentReviews->count() }} latest</span>
        </div>

        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            @forelse ($recentReviews as $review)
                <article class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-black text-slate-950">{{ $review->product?->name ?: 'Deleted product' }}</div>
                            <div class="mt-1 text-sm font-semibold text-slate-500">{{ $review->user?->name ?: 'Verified Buyer' }}</div>
                        </div>
                        <div class="flex items-center gap-1 text-amber-500">
                            @for ($i = 1; $i <= 5; $i++)
                                <x-icon name="star" class="h-4 w-4 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-300' }}" />
                            @endfor
                        </div>
                    </div>
                    <p class="mt-4 rounded-xl bg-white p-4 text-sm leading-6 text-slate-600">{{ $review->comment ?: 'No comment.' }}</p>

                    @if ($review->admin_reply)
                        <div class="mt-3 rounded-xl border border-teal-200 bg-teal-50 p-4 text-sm leading-6 text-teal-900">
                            <div class="font-black">Admin reply</div>
                            <p class="mt-1">{{ $review->admin_reply }}</p>
                            <div class="mt-2 text-xs font-bold text-teal-700">{{ $review->repliedBy?->name }} {{ $review->replied_at?->format('d M Y H:i') }}</div>
                        </div>
                    @endif

                    @if (auth()->user()->hasPermission('manage-orders'))
                        <form class="mt-3 grid gap-3" method="POST" action="{{ route('admin.reviews.reply', $review) }}">
                            @csrf
                            @method('PATCH')
                            <textarea class="min-h-24 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-teal-500" name="admin_reply" placeholder="Balas ulasan customer..." required>{{ old('admin_reply', $review->admin_reply) }}</textarea>
                            <button class="btn-primary w-max px-4 py-2 text-sm" type="submit">
                                <x-icon name="message" class="h-4 w-4" />
                                Reply Review
                            </button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-slate-600">
                    Belum ada ulasan produk dari customer.
                </div>
            @endforelse
        </div>
    </section>
@endsection
