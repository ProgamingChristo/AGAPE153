<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AboutController extends Controller
{
    public function __invoke(Request $request)
    {
        $requestedLocale = $request->string('lang')->lower()->toString();

        if (in_array($requestedLocale, ['id', 'en'], true)) {
            session(['locale' => $requestedLocale]);
            App::setLocale($requestedLocale);
        }

        $locale = App::getLocale();

        return view('about', [
            'locale' => $locale,
            'content' => $this->content($locale),
        ]);
    }

    private function content(string $locale): array
    {
        if ($locale === 'id') {
            return [
                'eyebrow' => 'Profil Perusahaan',
                'title' => 'Pertanian Indonesia untuk Perdagangan Komoditas Internasional',
                'lead' => 'Agape153 menghubungkan kekayaan agrikultur Indonesia dengan pembeli internasional melalui komoditas rempah, akar herbal, dan produk pertanian yang dikurasi secara profesional.',
                'overview' => [
                    'Didirikan pada tahun 2021, Agape153 memulai perjalanan dengan misi membawa sumber daya pertanian Indonesia ke pasar internasional.',
                    'Berbasis strategis di Jakarta, perusahaan memiliki akses yang kuat untuk koordinasi logistik, quality control, dan konektivitas perdagangan internasional.',
                    'Agape153 menggabungkan profesionalisme, nilai organik, transparansi, dan standar kualitas untuk membangun kepercayaan jangka panjang.',
                ],
                'vision' => 'Menjadi pemasok internasional terpercaya untuk komoditas pertanian Indonesia dengan integritas, kualitas premium, dan hubungan bisnis jangka panjang.',
                'mission' => 'Menyediakan produk yang memenuhi standar internasional, membangun kemitraan berkelanjutan, dan menjaga identitas Made in Indonesia di pasar dunia.',
                'pillars' => [
                    ['title' => 'Quality Assurance', 'body' => 'Purity testing, moisture control, microbial safety, and compliance with EU and international standards.'],
                    ['title' => 'Shipping Logistics', 'body' => 'Food-grade jute bags, vacuum packs, FOB/CIF shipping options, and complete shipping documentation.'],
                    ['title' => 'International Partnership', 'body' => 'Long-term relationships with importers, distributors, roasters, manufacturers, and institutional buyers.'],
                ],
            ];
        }

        return [
            'eyebrow' => 'Company Profile',
            'title' => 'Indonesian Agriculture for International Commodity Trading',
            'lead' => "Agape153 connects Indonesia's agricultural abundance with international buyers through carefully sourced spices, herbal roots, and premium agricultural commodities.",
            'overview' => [
                "Founded in 2021, Agape153 began with a clear mission: to bring Indonesia's natural richness into the international commodity market with integrity and excellence.",
                'Strategically headquartered in Jakarta, the economic heart of Indonesia, the company benefits from stronger logistics coordination, streamlined quality control, and international connectivity.',
                'From local supply roots to international trading, Agape153 combines organic values with professional execution, transparency, and quality discipline.',
            ],
            'vision' => 'To become a trusted international supplier of Indonesian agricultural commodities, recognized for integrity, premium quality, and long-term partnership.',
            'mission' => 'To deliver products that meet international standards, build sustainable partnerships, and represent the Made in Indonesia identity with confidence in international markets.',
            'pillars' => [
                ['title' => 'Quality Assurance', 'body' => 'Purity testing, microscopic analysis, moisture control, microbial safety, and compliance with EU and international standards.'],
                ['title' => 'Packaging & Shipping', 'body' => 'Food-grade jute bags, vacuum packaging, sustainable materials, FOB/CIF arrangements, and complete shipping documentation.'],
                ['title' => 'Partnership Focus', 'body' => 'Sustainable relationships with importers, distributors, roasters, manufacturers, and international institutional buyers.'],
            ],
        ];
    }
}
