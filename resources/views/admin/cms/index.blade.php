@extends('layouts.admin')

@section('title', 'CMS - Admin Agape153')

@section('content')
    <div>
        <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">CMS</p>
        <h1 class="mt-3 text-4xl font-black text-slate-950">Manage website content.</h1>
    </div>

    <div class="mt-8 grid gap-8">
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Homepage Sections</h2>
            <form class="mt-5 grid gap-3 md:grid-cols-2" method="POST" action="{{ route('admin.cms.sections.store') }}">
                @csrf
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="key" placeholder="Key">
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="page" value="home" placeholder="Page" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="title" placeholder="Title">
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="subtitle" placeholder="Subtitle">
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="image_url" placeholder="Image URL">
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="sort_order" type="number" value="0" placeholder="Sort">
                <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3 md:col-span-2" name="body" placeholder="Body"></textarea>
                <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" value="1" checked> Active</label>
                <button class="btn-primary md:justify-self-end" type="submit">Add Section</button>
            </form>

            <div class="mt-6 grid gap-4">
                @foreach ($sections as $section)
                    <form class="grid gap-3 rounded-2xl border border-slate-100 bg-[#f8faf9] p-4 md:grid-cols-2" method="POST" action="{{ route('admin.cms.sections.update', $section) }}">
                        @csrf
                        @method('PUT')
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="key" value="{{ $section->key }}" placeholder="Key">
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="page" value="{{ $section->page }}" placeholder="Page" required>
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="title" value="{{ $section->title }}" placeholder="Title">
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="subtitle" value="{{ $section->subtitle }}" placeholder="Subtitle">
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="image_url" value="{{ $section->image_url }}" placeholder="Image URL">
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="sort_order" type="number" value="{{ $section->sort_order }}">
                        <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3 md:col-span-2" name="body">{{ $section->body }}</textarea>
                        <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" value="1" @checked($section->is_active)> Active</label>
                        <button class="btn-secondary md:justify-self-end" type="submit">Save Section</button>
                    </form>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">FAQ</h2>
            <form class="mt-5 grid gap-3 md:grid-cols-[1fr_1fr_120px_auto]" method="POST" action="{{ route('admin.cms.faqs.store') }}">
                @csrf
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="question" placeholder="Question" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="answer" placeholder="Answer" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="sort_order" type="number" value="0">
                <button class="btn-primary" type="submit">Add FAQ</button>
                <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" value="1" checked> Active</label>
            </form>
            <div class="mt-5 grid gap-3">
                @foreach ($faqs as $faq)
                    <form class="grid gap-3 rounded-xl bg-[#f8faf9] p-4 md:grid-cols-[1fr_1fr_100px_auto]" method="POST" action="{{ route('admin.cms.faqs.update', $faq) }}">
                        @csrf
                        @method('PUT')
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="question" value="{{ $faq->question }}" required>
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="answer" value="{{ $faq->answer }}" required>
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="sort_order" type="number" value="{{ $faq->sort_order }}">
                        <button class="btn-secondary" type="submit">Save</button>
                        <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" value="1" @checked($faq->is_active)> Active</label>
                    </form>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Gallery</h2>
            <form class="mt-5 grid gap-3 md:grid-cols-2" method="POST" enctype="multipart/form-data" action="{{ route('admin.cms.galleries.store') }}">
                @csrf
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="title" placeholder="Title" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" type="file" name="image_file" accept="image/*">
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="image_url" placeholder="Image URL">
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="description" placeholder="Description">
                <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" value="1" checked> Active</label>
                <button class="btn-primary md:justify-self-end" type="submit">Add Gallery</button>
            </form>
            <div class="mt-5 grid gap-3 md:grid-cols-2">
                @foreach ($galleries as $gallery)
                    <form class="grid gap-3 rounded-xl bg-[#f8faf9] p-4" method="POST" enctype="multipart/form-data" action="{{ route('admin.cms.galleries.update', $gallery) }}">
                        @csrf
                        @method('PUT')
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="title" value="{{ $gallery->title }}" required>
                        <input class="rounded-xl border border-slate-200 px-4 py-3" type="file" name="image_file" accept="image/*">
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="image_url" value="{{ $gallery->image_url }}">
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="description" value="{{ $gallery->description }}">
                        <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" value="1" @checked($gallery->is_active)> Active</label>
                        <button class="btn-secondary" type="submit">Save Gallery</button>
                    </form>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Testimonials</h2>
            <form class="mt-5 grid gap-3 md:grid-cols-3" method="POST" action="{{ route('admin.cms.testimonials.store') }}">
                @csrf
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="name" placeholder="Name" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="company" placeholder="Company">
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="country" placeholder="Country">
                <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3 md:col-span-2" name="message" placeholder="Message" required></textarea>
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="rating" type="number" min="1" max="5" value="5">
                <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_active" value="1" checked> Active</label>
                <button class="btn-primary md:justify-self-end" type="submit">Add Testimonial</button>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">News</h2>
            <form class="mt-5 grid gap-3 md:grid-cols-2" method="POST" action="{{ route('admin.cms.news.store') }}">
                @csrf
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="title" placeholder="Title" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="slug" placeholder="Slug">
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="excerpt" placeholder="Excerpt">
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="image_url" placeholder="Image URL">
                <textarea class="min-h-28 rounded-xl border border-slate-200 px-4 py-3 md:col-span-2" name="content" placeholder="Content"></textarea>
                <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_published" value="1"> Published</label>
                <button class="btn-primary md:justify-self-end" type="submit">Add News</button>
            </form>
            <div class="mt-5 grid gap-3">
                @foreach ($newsPosts as $post)
                    <form class="grid gap-3 rounded-xl bg-[#f8faf9] p-4 md:grid-cols-3" method="POST" action="{{ route('admin.cms.news.update', $post) }}">
                        @csrf
                        @method('PUT')
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="title" value="{{ $post->title }}" required>
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="slug" value="{{ $post->slug }}">
                        <input class="rounded-xl border border-slate-200 px-4 py-3" name="image_url" value="{{ $post->image_url }}">
                        <input class="rounded-xl border border-slate-200 px-4 py-3 md:col-span-3" name="excerpt" value="{{ $post->excerpt }}">
                        <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3 md:col-span-3" name="content">{{ $post->content }}</textarea>
                        <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="is_published" value="1" @checked($post->is_published)> Published</label>
                        <button class="btn-secondary md:justify-self-end" type="submit">Save News</button>
                    </form>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Footer</h2>
            <form class="mt-5 grid gap-3" method="POST" action="{{ route('admin.cms.footer.update') }}">
                @csrf
                @method('PUT')
                <textarea class="min-h-24 rounded-xl border border-slate-200 px-4 py-3" name="footer_description" required>{{ $footerDescription }}</textarea>
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="company_address" value="{{ $siteContact['company_address'] ?? 'Indonesia' }}" placeholder="Company address">
                <input class="rounded-xl border border-slate-200 px-4 py-3" type="number" min="0" step="100000" name="high_value_order_threshold" value="{{ old('high_value_order_threshold', \App\Models\WebsiteSetting::value('high_value_order_threshold', '10000000')) }}" placeholder="High value order threshold">
                <button class="btn-primary justify-self-start" type="submit">Save Footer</button>
            </form>
        </section>
    </div>
@endsection
