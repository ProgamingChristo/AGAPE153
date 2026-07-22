<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

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

        $roasted = DB::table('products')
            ->where('slug', 'arabica-roasted')
            ->orWhere('sku', 'CF-ARABICA-ROASTED')
            ->orderBy('id')
            ->first();

        $oldProducts = DB::table('products')
            ->where('slug', 'arabica-green-beans')
            ->orWhere('sku', 'CF-ARABICA-GREEN')
            ->orWhere('name', 'Arabica Green Beans')
            ->orderBy('id')
            ->get();

        if ($roasted) {
            DB::table('products')->where('id', $roasted->id)->update($payload);

            foreach ($oldProducts as $oldProduct) {
                if ((int) $oldProduct->id === (int) $roasted->id) {
                    continue;
                }

                DB::table('products')->where('id', $oldProduct->id)->update([
                    'name' => 'Arabica Roasted',
                    'slug' => 'arabica-roasted-legacy-'.$oldProduct->id,
                    'sku' => 'CF-ARABICA-ROASTED-LEGACY-'.$oldProduct->id,
                    'short_description' => $payload['short_description'],
                    'description' => $payload['description'],
                    'grade' => 'Roasted Beans',
                    'image_url' => '/images/catalog/arabica-green-beans.jpg',
                    'is_active' => false,
                    'updated_at' => now(),
                ]);
            }
        } elseif ($oldProducts->isNotEmpty()) {
            $primary = $oldProducts->first();
            DB::table('products')->where('id', $primary->id)->update($payload);

            foreach ($oldProducts->skip(1) as $oldProduct) {
                DB::table('products')->where('id', $oldProduct->id)->update([
                    'name' => 'Arabica Roasted',
                    'slug' => 'arabica-roasted-legacy-'.$oldProduct->id,
                    'sku' => 'CF-ARABICA-ROASTED-LEGACY-'.$oldProduct->id,
                    'short_description' => $payload['short_description'],
                    'description' => $payload['description'],
                    'grade' => 'Roasted Beans',
                    'image_url' => '/images/catalog/arabica-green-beans.jpg',
                    'is_active' => false,
                    'updated_at' => now(),
                ]);
            }
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
    }
};
