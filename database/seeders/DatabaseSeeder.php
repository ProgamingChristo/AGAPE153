<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\NewsPost;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->upsert([
            ['name' => 'admin', 'label' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'member', 'label' => 'Member', 'created_at' => now(), 'updated_at' => now()],
        ], ['name'], ['label', 'updated_at']);

        collect([
            'manage-products' => 'Manage Products',
            'manage-categories' => 'Manage Categories',
            'manage-orders' => 'Manage Orders',
            'manage-content' => 'Manage Website Content',
            'view-analytics' => 'View Analytics',
        ])->each(fn ($label, $name) => DB::table('permissions')->updateOrInsert(
            ['name' => $name],
            ['label' => $label, 'created_at' => now(), 'updated_at' => now()]
        ));

        $admin = User::query()->updateOrCreate([
            'email' => 'admin@agape153.com',
        ], [
            'name' => 'Agape153 Admin',
            'password' => 'password',
            'phone' => '6281234567890',
            'company_name' => 'Agape153',
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $member = User::query()->updateOrCreate([
            'email' => 'member@agape153.com',
        ], [
            'name' => 'Demo Buyer',
            'password' => 'password',
            'phone' => '628111222333',
            'company_name' => 'Demo Trading',
            'role' => 'member',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $roleIds = DB::table('roles')->pluck('id', 'name');
        DB::table('role_user')->updateOrInsert(['role_id' => $roleIds['admin'], 'user_id' => $admin->id]);
        DB::table('role_user')->updateOrInsert(['role_id' => $roleIds['member'], 'user_id' => $member->id]);

        $categoryData = [
            ['name' => 'Rempah-rempah', 'type' => 'spice', 'description' => 'Lada, cengkeh, kayu manis, jahe, kunyit, pala, kemiri, ketumbar, dan rempah Indonesia pilihan.', 'image_url' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=900&q=80', 'sort_order' => 1],
            ['name' => 'Arabica Coffee', 'type' => 'coffee', 'description' => 'Kopi arabica Indonesia dengan karakter floral, citrus, spice, dan body elegan dari origin unggulan.', 'image_url' => 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=900&q=80', 'sort_order' => 2],
            ['name' => 'Robusta Coffee', 'type' => 'coffee', 'description' => 'Robusta Indonesia untuk espresso blend, retail, dan kebutuhan industri dengan body kuat.', 'image_url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=900&q=80', 'sort_order' => 3],
            ['name' => 'Export Products', 'type' => 'export', 'description' => 'Produk yang disiapkan untuk permintaan pembeli internasional, termasuk informasi origin, grade, dan MOQ.', 'image_url' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=900&q=80', 'sort_order' => 4],
        ];

        $categories = collect($categoryData)->mapWithKeys(function (array $data) {
            $category = Category::query()->updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [...$data, 'is_active' => true]
            );

            return [$category->name => $category];
        });

        foreach (['Gayo', 'Toraja', 'Kintamani', 'Flores', 'Java', 'Wamena', 'Ijen'] as $index => $name) {
            $categories[$name] = Category::query()->updateOrCreate(
                ['slug' => 'arabica-'.Str::slug($name)],
                [
                    'parent_id' => $categories['Arabica Coffee']->id,
                    'name' => "Arabica {$name}",
                    'type' => 'coffee',
                    'description' => "Arabica {$name} dikenal sebagai origin Indonesia dengan profil rasa khas, cocok untuk specialty, retail, dan buyer yang membutuhkan diferensiasi origin.",
                    'image_url' => 'https://images.unsplash.com/photo-1498804103079-a6351b050096?auto=format&fit=crop&w=900&q=80',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        foreach (['Lampung', 'Jambi', 'Temanggung', 'Bengkulu'] as $index => $name) {
            $categories[$name] = Category::query()->updateOrCreate(
                ['slug' => 'robusta-'.Str::slug($name)],
                [
                    'parent_id' => $categories['Robusta Coffee']->id,
                    'name' => "Robusta {$name}",
                    'type' => 'coffee',
                    'description' => "Robusta {$name} cocok untuk espresso blend, kebutuhan roasting komersial, dan pembeli yang mencari body kuat.",
                    'image_url' => 'https://images.unsplash.com/photo-1511537190424-bbbab87ac5eb?auto=format&fit=crop&w=900&q=80',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        $products = [
            ['category' => 'Jambi', 'name' => 'Robusta Grade A Jambi', 'sku' => 'RB-JBI-A', 'short_description' => 'Robusta grade A dari Jambi dengan body kuat, bitterness bersih, dan cocok untuk espresso blend.', 'origin' => 'Jambi', 'grade' => 'Grade A', 'price' => 80000, 'featured' => true, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1559525839-d9b6c2c04241?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Gayo', 'name' => 'Arabica Gayo Premium', 'sku' => 'AR-GYO-P', 'short_description' => 'Arabica Gayo dengan aroma kompleks, acidity seimbang, dan aftertaste manis.', 'origin' => 'Aceh Gayo', 'grade' => 'Premium', 'price' => 145000, 'featured' => true, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1559496417-e7f25cb247f3?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Toraja', 'name' => 'Arabica Toraja Specialty', 'sku' => 'AR-TRJ-S', 'short_description' => 'Karakter earthy, spice, dan body tebal dari dataran tinggi Toraja.', 'origin' => 'Toraja', 'grade' => 'Specialty', 'price' => 155000, 'featured' => true, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Kintamani', 'name' => 'Arabica Kintamani Bali', 'sku' => 'AR-KNT-B', 'short_description' => 'Profil citrus segar, aroma clean, dan cocok untuk filter coffee.', 'origin' => 'Bali Kintamani', 'grade' => 'Grade 1', 'price' => 150000, 'featured' => false, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Lampung', 'name' => 'Robusta Lampung Commercial', 'sku' => 'RB-LPG-C', 'short_description' => 'Robusta Lampung untuk kebutuhan komersial dengan pasokan stabil.', 'origin' => 'Lampung', 'grade' => 'Commercial', 'price' => 76000, 'featured' => false, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1611854779393-1b2da9d400fe?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Rempah-rempah', 'name' => 'Lada Hitam Indonesia', 'sku' => 'SP-LD-H', 'short_description' => 'Lada hitam aromatik untuk retail, horeca, dan kebutuhan ekspor.', 'origin' => 'Indonesia', 'grade' => 'Cleaned', 'price' => 98000, 'featured' => true, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1599909631715-3cda216f3bfb?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Rempah-rempah', 'name' => 'Lada Putih Indonesia', 'sku' => 'SP-LD-P', 'short_description' => 'Lada putih dengan aroma tajam dan warna bersih.', 'origin' => 'Indonesia', 'grade' => 'FAQ', 'price' => 126000, 'featured' => false, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1600628421060-cfb6f17d38b0?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Rempah-rempah', 'name' => 'Cengkeh Kering', 'sku' => 'SP-CGK', 'short_description' => 'Cengkeh kering untuk rempah, minuman, dan kebutuhan industri.', 'origin' => 'Indonesia', 'grade' => 'Dry', 'price' => 135000, 'featured' => true, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1615485500704-8e990f9900f7?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Rempah-rempah', 'name' => 'Kayu Manis Cassia', 'sku' => 'SP-KM-C', 'short_description' => 'Kayu manis cassia dengan aroma manis hangat untuk pasar lokal dan ekspor.', 'origin' => 'Indonesia', 'grade' => 'Stick', 'price' => 72000, 'featured' => false, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1608226791126-080906f57d72?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Rempah-rempah', 'name' => 'Jahe Kering', 'sku' => 'SP-JHE-K', 'short_description' => 'Jahe kering untuk bahan minuman, herbal, dan industri makanan.', 'origin' => 'Indonesia', 'grade' => 'Dry Slice', 'price' => 68000, 'featured' => false, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1603431778733-38b5b9fbb0d4?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Rempah-rempah', 'name' => 'Kunyit Kering', 'sku' => 'SP-KNY-K', 'short_description' => 'Kunyit kering dengan warna kuat untuk rempah dan bahan herbal.', 'origin' => 'Indonesia', 'grade' => 'Dry Slice', 'price' => 52000, 'featured' => false, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1615485500834-bc10199bc727?auto=format&fit=crop&w=900&q=80'],
            ['category' => 'Rempah-rempah', 'name' => 'Pala Indonesia', 'sku' => 'SP-PLA', 'short_description' => 'Pala pilihan dengan aroma hangat dan kualitas ekspor.', 'origin' => 'Indonesia', 'grade' => 'ABC', 'price' => 118000, 'featured' => false, 'export' => true, 'image' => 'https://images.unsplash.com/photo-1600628422019-8d2760b99674?auto=format&fit=crop&w=900&q=80'],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['slug' => Str::slug($product['name'])],
                [
                    'category_id' => $categories[$product['category']]->id,
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'short_description' => $product['short_description'],
                    'description' => $product['short_description']."\n\nProduk ini disiapkan untuk kebutuhan katalog digital Agape153 dengan harga dinamis yang dapat diperbarui melalui dashboard admin.",
                    'origin' => $product['origin'],
                    'grade' => $product['grade'],
                    'unit' => 'Kg',
                    'price' => $product['price'],
                    'currency' => 'IDR',
                    'min_order_quantity' => 1,
                    'stock_quantity' => 1000,
                    'is_featured' => $product['featured'],
                    'is_active' => true,
                    'export_ready' => $product['export'],
                    'image_url' => $product['image'],
                    'meta_title' => $product['name'].' - Agape153',
                    'meta_description' => $product['short_description'],
                ]
            );
        }

        foreach ([
            ['title' => 'Spice Sorting', 'image_url' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=900&q=80', 'description' => 'Kurasi rempah pilihan.'],
            ['title' => 'Coffee Beans', 'image_url' => 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=900&q=80', 'description' => 'Biji kopi Indonesia.'],
            ['title' => 'Export Preparation', 'image_url' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=900&q=80', 'description' => 'Persiapan order pembeli global.'],
        ] as $gallery) {
            Gallery::query()->updateOrCreate(['title' => $gallery['title']], [...$gallery, 'is_active' => true]);
        }

        foreach ([
            ['name' => 'Ari Pratama', 'company' => 'Retail Buyer', 'country' => 'Indonesia', 'message' => 'Katalognya jelas dan memudahkan tim kami memilih produk sebelum konfirmasi stok.'],
            ['name' => 'M. Khalid', 'company' => 'Importer', 'country' => 'UAE', 'message' => 'Informasi origin dan grade membantu proses inquiry ekspor menjadi lebih cepat.'],
            ['name' => 'Sofia Martin', 'company' => 'Coffee Roaster', 'country' => 'Spain', 'message' => 'Pilihan arabica dan robusta Indonesia tersaji rapi untuk kebutuhan sourcing.'],
        ] as $testimonial) {
            Testimonial::query()->updateOrCreate(['name' => $testimonial['name']], [...$testimonial, 'rating' => 5, 'is_active' => true]);
        }

        foreach ([
            ['question' => 'Apakah harga bisa berubah?', 'answer' => 'Ya. Harga produk bersifat dinamis dan dapat diperbarui melalui dashboard admin sesuai stok, grade, dan kondisi pasar.'],
            ['question' => 'Apakah melayani ekspor?', 'answer' => 'Website disiapkan untuk buyer internasional. Detail dokumen, MOQ, dan pengiriman perlu dikonfirmasi oleh tim sales.'],
            ['question' => 'Bagaimana cara order?', 'answer' => 'Pilih produk, masukkan ke cart, checkout, lalu konfirmasi order melalui WhatsApp.'],
            ['question' => 'Apakah Midtrans tersedia?', 'answer' => 'Struktur payment sudah disiapkan. Integrasi Midtrans dapat diaktifkan pada fase pengembangan lanjutan.'],
        ] as $index => $faq) {
            Faq::query()->updateOrCreate(['question' => $faq['question']], [...$faq, 'is_active' => true, 'sort_order' => $index + 1]);
        }

        NewsPost::query()->updateOrCreate([
            'slug' => 'agape153-digital-catalog-launch',
        ], [
            'title' => 'Agape153 Digital Catalog Launch',
            'excerpt' => 'Katalog digital Agape153 membantu pembeli melihat rempah dan kopi Indonesia secara cepat.',
            'content' => 'Agape153 mengembangkan katalog digital untuk mempercepat proses inquiry produk lokal dan ekspor.',
            'image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80',
            'is_published' => true,
            'published_at' => now(),
            'meta_title' => 'Agape153 Digital Catalog Launch',
            'meta_description' => 'Katalog digital rempah dan kopi Indonesia dari Agape153.',
        ]);

        foreach ([
            'whatsapp_number' => '6281234567890',
            'company_email' => 'hello@agape153.com',
            'company_address' => 'Indonesia',
            'seo_title' => 'Agape153 - Indonesian Spices & Coffee Export',
            'seo_description' => 'Supplier rempah-rempah dan kopi Indonesia untuk lokal dan ekspor.',
        ] as $key => $value) {
            DB::table('website_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'group' => 'general', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
