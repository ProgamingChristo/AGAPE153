<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\ContentTranslation;
use App\Models\Product;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            $fontOptions = config('agape.font_families', []);
            $fontKey = WebsiteSetting::value('appearance_font_family', 'plus_jakarta');
            $font = $fontOptions[$fontKey] ?? $fontOptions['plus_jakarta'] ?? [
                'label' => 'Plus Jakarta Sans',
                'url' => 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap',
                'stack' => "'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif",
            ];

            $view->with('siteContact', [
                'phone' => WebsiteSetting::value('phone_number', '+62816795153'),
                'whatsapp' => WebsiteSetting::value('whatsapp_number', '+62816795153'),
                'email' => WebsiteSetting::value('company_email', 'info.agape153@gmail.com'),
                'youtube_url' => WebsiteSetting::value('youtube_url', 'https://www.youtube.com/@AGAPE153CHANNEL'),
                'instagram_handle' => WebsiteSetting::value('instagram_handle', '@agape153.official'),
                'instagram_url' => WebsiteSetting::value('instagram_url', 'https://www.instagram.com/agape153.official'),
                'facebook_url' => WebsiteSetting::value('facebook_url', 'https://www.facebook.com/profile.php?id=61590494259264'),
                'linkedin_url' => WebsiteSetting::value('linkedin_url', 'https://www.linkedin.com/in/agape153'),
                'tiktok_url' => WebsiteSetting::value('tiktok_url', 'https://www.tiktok.com/@agape153.official'),
                'threads_handle' => WebsiteSetting::value('threads_handle', '@agape153.official'),
                'threads_url' => WebsiteSetting::value('threads_url', 'https://www.threads.net/@agape153.official'),
                'footer_description' => WebsiteSetting::value('footer_description', 'Supplier rempah-rempah dan kopi Indonesia untuk pembeli lokal, distributor, horeca, dan importir international.'),
                'company_address' => WebsiteSetting::value('company_address', 'Indonesia'),
            ]);
            $view->with('siteAppearance', [
                'primary_color' => WebsiteSetting::value('appearance_primary_color', '#0f766e'),
                'accent_color' => WebsiteSetting::value('appearance_accent_color', '#e9c95a'),
                'soft_color' => WebsiteSetting::value('appearance_soft_color', '#edf7f4'),
                'homepage_layout' => WebsiteSetting::value('appearance_homepage_layout', 'classic'),
                'font_family' => $fontKey,
                'font_label' => $font['label'],
                'font_url' => $font['url'],
                'font_stack' => $font['stack'],
                'hero_badge' => WebsiteSetting::value('appearance_hero_badge', 'Indonesian spices and coffee supplier'),
                'hero_title' => WebsiteSetting::value('appearance_hero_title', 'Agape153'),
                'hero_subtitle' => WebsiteSetting::value('appearance_hero_subtitle', 'Katalog rempah-rempah, kopi arabica, dan robusta Indonesia untuk pembeli lokal, retail, horeca, distributor, dan importir internasional.'),
                'hero_image_url' => WebsiteSetting::value('appearance_hero_image_url', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=900&q=80'),
                'hero_slides' => [
                    WebsiteSetting::value('appearance_hero_image_url', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=1800&q=80'),
                    WebsiteSetting::value('appearance_hero_slide_2_url', 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=1800&q=80'),
                    WebsiteSetting::value('appearance_hero_slide_3_url', 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1800&q=80'),
                ],
                'show_gallery' => WebsiteSetting::value('appearance_show_gallery', '1') === '1',
                'show_testimonials' => WebsiteSetting::value('appearance_show_testimonials', '1') === '1',
            ]);
            $googleClientId = WebsiteSetting::value('google_client_id') ?: config('services.google.client_id');
            $googleClientSecret = WebsiteSetting::value('google_client_secret') ?: config('services.google.client_secret');
            $view->with('googleOAuthEnabled', filled($googleClientId) && filled($googleClientSecret));
            $view->with('siteFooterStats', [
                'catalog_lines' => Category::query()->active()->count(),
                'featured_skus' => Product::query()->active()->featured()->count(),
            ]);
            $locale = app()->getLocale();
            $fallbackText = trans('site');
            $fallbackText = is_array($fallbackText) ? $fallbackText : [];
            $customText = ContentTranslation::query()
                ->where('locale', $locale)
                ->pluck('value', 'key')
                ->all();

            $view->with('siteText', array_merge($fallbackText, $customText));
        });
    }
}
