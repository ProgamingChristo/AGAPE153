@extends('layouts.admin')

@section('title', ($product->exists ? 'Edit Product' : 'Add Product').' - Admin Agape153')

@section('content')
    @php
        $oldDetailLabels = old('detail_labels');
        $oldDetailValues = old('detail_values', []);
        $detailRows = collect(is_array($oldDetailLabels) ? $oldDetailLabels : ($product->product_details ?: []))
            ->map(function ($detail, $index) use ($oldDetailLabels, $oldDetailValues) {
                if (is_array($oldDetailLabels)) {
                    return [
                        'label' => $detail,
                        'value' => $oldDetailValues[$index] ?? '',
                    ];
                }

                return [
                    'label' => $detail['label'] ?? '',
                    'value' => $detail['value'] ?? '',
                ];
            })
            ->filter(fn ($detail) => ($detail['label'] ?? '') !== '' || ($detail['value'] ?? '') !== '')
            ->values();

        if ($detailRows->isEmpty()) {
            $detailRows = collect([
                ['label' => '', 'value' => ''],
            ]);
        }
    @endphp

    <div>
        <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Products</p>
        <h1 class="mt-3 text-4xl font-black text-slate-950">{{ $product->exists ? 'Edit product' : 'Add product' }}.</h1>
    </div>

    <form class="mt-8 grid gap-6 rounded-2xl border border-slate-200 bg-white p-6" method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if ($product->exists)
            @method('PUT')
        @endif
        <div class="grid gap-4 md:grid-cols-2">
            <label class="grid gap-2 text-sm font-bold">Category
                <select class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="grid gap-2 text-sm font-bold">Name
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="name" value="{{ old('name', $product->name) }}" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">Slug
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="slug" value="{{ old('slug', $product->slug) }}">
            </label>
            <label class="grid gap-2 text-sm font-bold">SKU
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="sku" value="{{ old('sku', $product->sku) }}">
            </label>
            <label class="grid gap-2 text-sm font-bold">Origin
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="origin" value="{{ old('origin', $product->origin) }}">
            </label>
            <label class="grid gap-2 text-sm font-bold">Grade
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="grade" value="{{ old('grade', $product->grade) }}">
            </label>
            <label class="grid gap-2 text-sm font-bold">Unit
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="unit" value="{{ old('unit', $product->unit ?: 'Kg') }}" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">Price
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" type="number" name="price" value="{{ old('price', $product->price) }}" min="0" step="100">
            </label>
            <label class="grid gap-2 text-sm font-bold">Currency
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="currency" value="{{ old('currency', $product->currency ?: 'IDR') }}" maxlength="3" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">Stock
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 20000) }}" min="0" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">MOQ
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" type="number" name="min_order_quantity" value="{{ old('min_order_quantity', $product->min_order_quantity ?: 100) }}" min="1" required>
            </label>
            <label class="grid gap-2 text-sm font-bold">Product Image
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal file:mr-4 file:rounded-lg file:border-0 file:bg-teal-700 file:px-3 file:py-2 file:text-sm file:font-bold file:text-white" type="file" name="image_file" accept="image/*">
            </label>
            <label class="grid gap-2 text-sm font-bold">Product Video URL
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="video_url" value="{{ old('video_url', $product->video_url) }}" placeholder="YouTube, Vimeo, MP4/WebM, or internal page URL">
            </label>
            <label class="grid gap-2 text-sm font-bold">Upload Product Video
                <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal file:mr-4 file:rounded-lg file:border-0 file:bg-slate-950 file:px-3 file:py-2 file:text-sm file:font-bold file:text-white" type="file" name="video_file" accept="video/mp4,video/webm,video/ogg">
            </label>
        </div>
        @if ($product->image_url || $product->video_url)
            <div class="grid gap-4 md:grid-cols-2">
                @if ($product->image_url)
                    <div class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-4">
                        <div class="mb-3 text-sm font-bold text-slate-700">Current image</div>
                        <img class="h-40 w-40 rounded-xl object-cover" src="{{ $product->image_url }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $product->name }}">
                    </div>
                @endif
                @if ($product->video_url)
                    @php
                        $videoEmbedUrl = $product->videoEmbedUrl();
                        $videoFileUrl = $product->videoFileUrl();
                    @endphp
                    <div class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-4">
                        <div class="mb-3 text-sm font-bold text-slate-700">Current video</div>
                        @if ($videoFileUrl)
                            <video class="aspect-video w-full rounded-xl bg-slate-950 object-cover" src="{{ $videoFileUrl }}" controls muted preload="metadata"></video>
                        @elseif ($videoEmbedUrl)
                            <iframe class="aspect-video w-full rounded-xl bg-slate-950" src="{{ $videoEmbedUrl }}" title="{{ $product->name }} video preview" loading="lazy" allowfullscreen></iframe>
                        @else
                            <a class="btn-secondary" href="{{ $product->video_url }}" target="_blank" rel="noopener">
                                <x-icon name="video" class="h-4 w-4" />
                                Open Video
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @endif
        <label class="grid gap-2 text-sm font-bold">Short Description
            <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3 font-normal" name="short_description">{{ old('short_description', $product->short_description) }}</textarea>
        </label>
        <label class="grid gap-2 text-sm font-bold">Description
            <textarea class="min-h-40 rounded-xl border border-slate-200 px-4 py-3 font-normal" name="description">{{ old('description', $product->description) }}</textarea>
        </label>
        <section class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-5">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-teal-700">Product Details</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-950">Structured buyer specifications.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Tambahkan detail yang akan tampil di halaman produk, misalnya Packaging, Moisture, Shelf life, Process, atau Certification.</p>
                </div>
                <button class="btn-secondary w-full shrink-0 px-4 py-2 text-sm sm:w-auto" type="button" data-add-product-detail>
                    <x-icon name="plus" class="h-4 w-4" />
                    Add Detail
                </button>
            </div>

            <div class="mt-5 grid gap-3" data-product-details-list>
                @foreach ($detailRows as $detail)
                    <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-3 md:grid-cols-[220px_1fr_auto]" data-product-detail-row>
                        <label class="grid gap-2 text-xs font-black uppercase tracking-[0.12em] text-slate-500">Label
                            <input class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-normal normal-case tracking-normal text-slate-900" name="detail_labels[]" value="{{ $detail['label'] ?? '' }}" placeholder="Packaging">
                        </label>
                        <label class="grid gap-2 text-xs font-black uppercase tracking-[0.12em] text-slate-500">Value
                            <input class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-normal normal-case tracking-normal text-slate-900" name="detail_values[]" value="{{ $detail['value'] ?? '' }}" placeholder="Food grade jute bag / vacuum pack">
                        </label>
                        <button class="self-end rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100 md:w-auto" type="button" title="Remove detail" aria-label="Remove detail" data-remove-product-detail>
                            <x-icon name="trash" class="h-4 w-4" />
                        </button>
                    </div>
                @endforeach
            </div>

            <template data-product-detail-template>
                <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-3 md:grid-cols-[220px_1fr_auto]" data-product-detail-row>
                    <label class="grid gap-2 text-xs font-black uppercase tracking-[0.12em] text-slate-500">Label
                        <input class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-normal normal-case tracking-normal text-slate-900" name="detail_labels[]" placeholder="Moisture">
                    </label>
                    <label class="grid gap-2 text-xs font-black uppercase tracking-[0.12em] text-slate-500">Value
                        <input class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-normal normal-case tracking-normal text-slate-900" name="detail_values[]" placeholder="Max 12%">
                    </label>
                    <button class="self-end rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100 md:w-auto" type="button" title="Remove detail" aria-label="Remove detail" data-remove-product-detail>
                        <x-icon name="trash" class="h-4 w-4" />
                    </button>
                </div>
            </template>
        </section>
        <div class="grid gap-4 md:grid-cols-3">
            <label class="flex items-center gap-2 text-sm font-bold">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
                Active
            </label>
            <label class="flex items-center gap-2 text-sm font-bold">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))>
                Featured
            </label>
            <label class="flex items-center gap-2 text-sm font-bold">
                <input type="checkbox" name="export_ready" value="1" @checked(old('export_ready', $product->export_ready))>
                Export ready
            </label>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <button class="btn-primary sm:w-auto" type="submit">Save Product</button>
            <a class="btn-secondary sm:w-auto" href="{{ route('admin.products.index') }}">Cancel</a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const list = document.querySelector('[data-product-details-list]');
            const addButton = document.querySelector('[data-add-product-detail]');
            const template = document.querySelector('[data-product-detail-template]');

            if (!list || !addButton || !template) {
                return;
            }

            const bindRemoveButton = (row) => {
                row.querySelector('[data-remove-product-detail]')?.addEventListener('click', () => {
                    const rows = list.querySelectorAll('[data-product-detail-row]');

                    if (rows.length === 1) {
                        row.querySelectorAll('input').forEach((input) => {
                            input.value = '';
                        });

                        return;
                    }

                    row.remove();
                });
            };

            list.querySelectorAll('[data-product-detail-row]').forEach(bindRemoveButton);

            addButton.addEventListener('click', () => {
                const fragment = template.content.cloneNode(true);
                const row = fragment.querySelector('[data-product-detail-row]');

                list.appendChild(fragment);
                bindRemoveButton(row);
                row.querySelector('input')?.focus();
            });
        });
    </script>
@endsection
