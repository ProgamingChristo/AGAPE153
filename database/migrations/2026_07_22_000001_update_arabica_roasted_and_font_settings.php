<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            $payload = [
                'name' => 'Arabica Roasted',
                'slug' => 'arabica-roasted',
                'sku' => 'CF-ARABICA-ROASTED',
                'short_description' => 'Roasted Indonesian arabica coffee beans with warm aroma for retail, horeca, gift packs, and buyer-ready coffee supply.',
                'description' => "Roasted Indonesian arabica coffee beans with warm aroma for retail, horeca, gift packs, and buyer-ready coffee supply.\n\nProduk ini disiapkan untuk kebutuhan katalog digital Agape153 dengan harga dinamis yang dapat diperbarui melalui dashboard admin.",
                'grade' => 'Roasted Beans',
                'image_url' => '/images/catalog/arabica-green-beans.jpg',
                'meta_title' => 'Arabica Roasted - Agape153',
                'meta_description' => 'Roasted Indonesian arabica coffee beans with warm aroma for retail, horeca, gift packs, and buyer-ready coffee supply.',
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('products', 'product_details')) {
                $payload['product_details'] = json_encode([
                    ['label' => 'Form', 'value' => 'Roasted arabica coffee beans'],
                    ['label' => 'Profile', 'value' => 'Warm aroma, clean cup, and origin-dependent sweetness'],
                    ['label' => 'Buyer Use', 'value' => 'Retail coffee, horeca, gifting, and distribution'],
                ]);
            }

            if (Schema::hasColumn('products', 'video_url')) {
                $payload['video_url'] = '/videos/catalog/arabica-green-beans.html';
            }

            $existingArabicaRoasted = DB::table('products')
                ->where('slug', 'arabica-roasted')
                ->exists();

            DB::table('products')
                ->where(function ($query) use ($existingArabicaRoasted): void {
                    if ($existingArabicaRoasted) {
                        $query->where('slug', 'arabica-roasted')
                            ->orWhere('sku', 'CF-ARABICA-ROASTED');

                        return;
                    }

                    $query->where('slug', 'arabica-green-beans')
                        ->orWhere('sku', 'CF-ARABICA-GREEN')
                        ->orWhere('name', 'Arabica Green Beans');
                })
                ->update($payload);
        }

        if (Schema::hasTable('categories')) {
            DB::table('categories')
                ->where('slug', 'green-beans-arabica')
                ->orWhere('name', 'Arabica')
                ->update([
                    'description' => 'Arabica roasted coffee beans for Agape153 coffee sourcing catalog.',
                    'image_url' => '/images/catalog/arabica-green-beans.jpg',
                    'updated_at' => now(),
                ]);
        }

        if (Schema::hasTable('website_settings')) {
            DB::table('website_settings')->updateOrInsert(
                ['key' => 'appearance_font_family'],
                ['value' => 'plus_jakarta', 'group' => 'appearance', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            $payload = [
                'name' => 'Arabica Green Beans',
                'slug' => 'arabica-green-beans',
                'sku' => 'CF-ARABICA-GREEN',
                'short_description' => 'Indonesian arabica green beans for specialty roasters, distributors, and international buyers.',
                'description' => "Indonesian arabica green beans for specialty roasters, distributors, and international buyers.\n\nProduk ini disiapkan untuk kebutuhan katalog digital Agape153 dengan harga dinamis yang dapat diperbarui melalui dashboard admin.",
                'grade' => 'Green Beans',
                'image_url' => '/images/catalog/arabica-green-beans.jpg',
                'meta_title' => 'Arabica Green Beans - Agape153',
                'meta_description' => 'Indonesian arabica green beans for specialty roasters, distributors, and international buyers.',
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('products', 'video_url')) {
                $payload['video_url'] = '/videos/catalog/arabica-green-beans.html';
            }

            DB::table('products')
                ->where('slug', 'arabica-roasted')
                ->where('sku', 'CF-ARABICA-ROASTED')
                ->update($payload);
        }
    }
};
