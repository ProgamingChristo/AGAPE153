<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AppearanceController extends Controller
{
    public function edit()
    {
        return view('admin.appearance.edit', [
            'appearance' => $this->appearance(),
            'fontOptions' => config('agape.font_families', []),
        ]);
    }

    public function update(Request $request)
    {
        if (! $request->filled('font_family')) {
            $request->merge([
                'font_family' => WebsiteSetting::value('appearance_font_family', 'plus_jakarta'),
            ]);
        }

        $data = $request->validate([
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'soft_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'homepage_layout' => ['required', 'in:classic,compact,catalog_first'],
            'font_family' => ['required', 'in:'.implode(',', array_keys(config('agape.font_families', [])))],
            'hero_badge' => ['required', 'string', 'max:120'],
            'hero_title' => ['required', 'string', 'max:120'],
            'hero_subtitle' => ['required', 'string', 'max:260'],
            'hero_image_url' => ['nullable', 'url', 'max:500'],
            'hero_image_file' => ['nullable', 'image', 'max:4096'],
            'hero_slide_2_url' => ['nullable', 'url', 'max:500'],
            'hero_slide_2_file' => ['nullable', 'image', 'max:4096'],
            'hero_slide_3_url' => ['nullable', 'url', 'max:500'],
            'hero_slide_3_file' => ['nullable', 'image', 'max:4096'],
            'show_gallery' => ['nullable', 'boolean'],
            'show_testimonials' => ['nullable', 'boolean'],
            'google_client_id' => ['nullable', 'string', 'max:500'],
            'google_client_secret' => ['nullable', 'string', 'max:500'],
            'google_redirect_uri' => ['nullable', 'url', 'max:500'],
        ]);

        unset($data['hero_image_file'], $data['hero_slide_2_file'], $data['hero_slide_3_file']);

        if ($request->hasFile('hero_image_file')) {
            $path = $request->file('hero_image_file')->store('appearance', 'public');
            $data['hero_image_url'] = Storage::url($path);
        }

        foreach (['hero_slide_2', 'hero_slide_3'] as $slideKey) {
            if ($request->hasFile("{$slideKey}_file")) {
                $path = $request->file("{$slideKey}_file")->store('appearance', 'public');
                $data["{$slideKey}_url"] = Storage::url($path);
            }
        }

        $data['show_gallery'] = $request->boolean('show_gallery') ? '1' : '0';
        $data['show_testimonials'] = $request->boolean('show_testimonials') ? '1' : '0';

        $appearanceKeys = [
            'primary_color',
            'accent_color',
            'soft_color',
            'homepage_layout',
            'font_family',
            'hero_badge',
            'hero_title',
            'hero_subtitle',
            'hero_image_url',
            'hero_slide_2_url',
            'hero_slide_3_url',
            'show_gallery',
            'show_testimonials',
        ];

        foreach ($appearanceKeys as $key) {
            $value = $data[$key] ?? null;
            WebsiteSetting::query()->updateOrCreate(
                ['key' => "appearance_{$key}"],
                ['value' => $value, 'group' => 'appearance']
            );
            Cache::forget("setting:appearance_{$key}");
        }

        foreach (['google_client_id', 'google_redirect_uri'] as $key) {
            WebsiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $data[$key] ?? null, 'group' => 'integrations']
            );
            Cache::forget("setting:{$key}");
        }

        if (filled($data['google_client_secret'] ?? null)) {
            WebsiteSetting::query()->updateOrCreate(
                ['key' => 'google_client_secret'],
                ['value' => $data['google_client_secret'], 'group' => 'integrations']
            );
            Cache::forget('setting:google_client_secret');
        }

        return back()->with('status', 'Appearance settings updated.');
    }

    private function appearance(): array
    {
        return [
            'primary_color' => WebsiteSetting::value('appearance_primary_color', '#0f766e'),
            'accent_color' => WebsiteSetting::value('appearance_accent_color', '#e9c95a'),
            'soft_color' => WebsiteSetting::value('appearance_soft_color', '#edf7f4'),
            'homepage_layout' => WebsiteSetting::value('appearance_homepage_layout', 'classic'),
            'font_family' => WebsiteSetting::value('appearance_font_family', 'plus_jakarta'),
            'hero_badge' => WebsiteSetting::value('appearance_hero_badge', 'Indonesian spices and coffee supplier'),
            'hero_title' => WebsiteSetting::value('appearance_hero_title', 'Agape153'),
            'hero_subtitle' => WebsiteSetting::value('appearance_hero_subtitle', 'Katalog rempah-rempah, kopi arabica, dan robusta Indonesia untuk pembeli lokal, retail, horeca, distributor, dan importir internasional.'),
            'hero_image_url' => WebsiteSetting::value('appearance_hero_image_url', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=900&q=80'),
            'hero_slide_2_url' => WebsiteSetting::value('appearance_hero_slide_2_url', 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=1800&q=80'),
            'hero_slide_3_url' => WebsiteSetting::value('appearance_hero_slide_3_url', 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1800&q=80'),
            'show_gallery' => WebsiteSetting::value('appearance_show_gallery', '1'),
            'show_testimonials' => WebsiteSetting::value('appearance_show_testimonials', '1'),
            'google_client_id' => WebsiteSetting::value('google_client_id', config('services.google.client_id')),
            'google_client_secret_set' => filled(WebsiteSetting::value('google_client_secret', config('services.google.client_secret'))),
            'google_redirect_uri' => WebsiteSetting::value('google_redirect_uri', config('services.google.redirect') ?: url('/auth/google/callback')),
        ];
    }
}
