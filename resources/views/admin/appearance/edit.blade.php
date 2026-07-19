@extends('layouts.admin')

@section('title', 'Appearance - Admin Agape153')

@section('content')
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Appearance</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">Customize website layout.</h1>
            <p class="mt-3 max-w-2xl text-sm font-semibold text-slate-500">Atur warna utama, hero, dan section homepage tanpa menyentuh code.</p>
        </div>
        <a class="btn-secondary" href="{{ route('home') }}" target="_blank" rel="noopener"><x-icon name="home" class="h-4 w-4" />Preview Website</a>
    </div>

    <form class="mt-8 grid gap-6 rounded-2xl border border-slate-200 bg-white p-6" method="POST" enctype="multipart/form-data" action="{{ route('admin.appearance.update') }}">
        @csrf
        @method('PUT')

        <section>
            <h2 class="text-xl font-black text-slate-950">Theme Colors</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <label class="grid gap-2 text-sm font-bold text-slate-700">Primary Color
                    <input class="h-12 rounded-xl border border-slate-200 px-3" type="color" name="primary_color" value="{{ old('primary_color', $appearance['primary_color']) }}">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Accent Color
                    <input class="h-12 rounded-xl border border-slate-200 px-3" type="color" name="accent_color" value="{{ old('accent_color', $appearance['accent_color']) }}">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Soft Background
                    <input class="h-12 rounded-xl border border-slate-200 px-3" type="color" name="soft_color" value="{{ old('soft_color', $appearance['soft_color']) }}">
                </label>
            </div>
        </section>

        <section class="border-t border-slate-100 pt-6">
            <h2 class="text-xl font-black text-slate-950">Homepage Layout</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="grid gap-2 text-sm font-bold text-slate-700">Layout Style
                    <select class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="homepage_layout">
                        @foreach (['classic' => 'Classic with large hero', 'compact' => 'Compact buyer-focused hero', 'catalog_first' => 'Catalog-first homepage'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('homepage_layout', $appearance['homepage_layout']) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Hero Badge
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="hero_badge" value="{{ old('hero_badge', $appearance['hero_badge']) }}">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Hero Title
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="hero_title" value="{{ old('hero_title', $appearance['hero_title']) }}">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Hero Image URL
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="hero_image_url" value="{{ old('hero_image_url', $appearance['hero_image_url']) }}">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Upload Hero Image
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal file:mr-4 file:rounded-lg file:border-0 file:bg-teal-700 file:px-3 file:py-2 file:text-sm file:font-bold file:text-white" type="file" name="hero_image_file" accept="image/*">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Slideshow Image 2 URL
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="hero_slide_2_url" value="{{ old('hero_slide_2_url', $appearance['hero_slide_2_url']) }}">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Upload Slideshow Image 2
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal file:mr-4 file:rounded-lg file:border-0 file:bg-teal-700 file:px-3 file:py-2 file:text-sm file:font-bold file:text-white" type="file" name="hero_slide_2_file" accept="image/*">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Slideshow Image 3 URL
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="hero_slide_3_url" value="{{ old('hero_slide_3_url', $appearance['hero_slide_3_url']) }}">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Upload Slideshow Image 3
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal file:mr-4 file:rounded-lg file:border-0 file:bg-teal-700 file:px-3 file:py-2 file:text-sm file:font-bold file:text-white" type="file" name="hero_slide_3_file" accept="image/*">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700 md:col-span-2">Hero Subtitle
                    <textarea class="min-h-28 rounded-xl border border-slate-200 px-4 py-3 font-normal" name="hero_subtitle">{{ old('hero_subtitle', $appearance['hero_subtitle']) }}</textarea>
                </label>
            </div>
            @if ($appearance['hero_image_url'] || $appearance['hero_slide_2_url'] || $appearance['hero_slide_3_url'])
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    @foreach ([
                        'Hero image' => $appearance['hero_image_url'],
                        'Slideshow image 2' => $appearance['hero_slide_2_url'],
                        'Slideshow image 3' => $appearance['hero_slide_3_url'],
                    ] as $label => $imageUrl)
                        @if ($imageUrl)
                            <div class="rounded-2xl border border-slate-200 bg-[#f8faf9] p-4">
                                <div class="mb-3 text-sm font-bold text-slate-700">{{ $label }}</div>
                                <img class="h-36 w-full rounded-xl object-cover" src="{{ $imageUrl }}" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'" alt="{{ $label }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </section>

        <section class="border-t border-slate-100 pt-6">
            <div class="flex flex-col justify-between gap-2 md:flex-row md:items-start">
                <div>
                    <h2 class="text-xl font-black text-slate-950">Integrations</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Google OAuth mengikuti pola Laravel Socialite: Client ID, Client Secret, dan Redirect URI harus sama dengan Google Cloud Console.</p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-black {{ $appearance['google_client_secret_set'] && $appearance['google_client_id'] ? 'bg-teal-100 text-teal-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $appearance['google_client_secret_set'] && $appearance['google_client_id'] ? 'Google Ready' : 'Google Needs Key' }}
                </span>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="grid gap-2 text-sm font-bold text-slate-700">Google Client ID
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="google_client_id" value="{{ old('google_client_id', $appearance['google_client_id']) }}" placeholder="xxxxx.apps.googleusercontent.com">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700">Google Client Secret
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" type="password" name="google_client_secret" placeholder="{{ $appearance['google_client_secret_set'] ? 'Secret already saved, leave blank to keep it' : 'Paste Google client secret' }}">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700 md:col-span-2">Google Redirect URI
                    <input class="rounded-xl border border-slate-200 px-4 py-3 font-normal" name="google_redirect_uri" value="{{ old('google_redirect_uri', $appearance['google_redirect_uri']) }}" placeholder="{{ url('/auth/google/callback') }}">
                </label>
            </div>
        </section>

        <section class="border-t border-slate-100 pt-6">
            <h2 class="text-xl font-black text-slate-950">Homepage Sections</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 text-sm font-bold">
                    <input type="checkbox" name="show_gallery" value="1" @checked(old('show_gallery', $appearance['show_gallery']) === '1')>
                    Show Gallery Section
                </label>
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 text-sm font-bold">
                    <input type="checkbox" name="show_testimonials" value="1" @checked(old('show_testimonials', $appearance['show_testimonials']) === '1')>
                    Show Testimonials Section
                </label>
            </div>
        </section>

        <div class="flex flex-wrap gap-3 border-t border-slate-100 pt-6">
            <button class="btn-primary" type="submit"><x-icon name="settings" class="h-4 w-4" />Save Appearance</button>
            <a class="btn-secondary" href="{{ route('admin.dashboard') }}">Cancel</a>
        </div>
    </form>
@endsection
