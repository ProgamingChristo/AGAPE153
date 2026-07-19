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
            ['name' => 'staff', 'label' => 'Staff Admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'member', 'label' => 'Member', 'created_at' => now(), 'updated_at' => now()],
        ], ['name'], ['label', 'updated_at']);

        collect([
            'manage-products' => 'Manage Products',
            'manage-categories' => 'Manage Categories',
            'manage-orders' => 'Manage Orders',
            'manage-messages' => 'Manage Contact Messages',
            'manage-cms' => 'Manage CMS and Appearance',
            'manage-users' => 'Manage Staff and Permissions',
            'view-reports' => 'View Reports',
            'view-analytics' => 'View Analytics',
            'manage-payments' => 'Manage Payments',
        ])->each(fn ($label, $name) => DB::table('permissions')->updateOrInsert(
            ['name' => $name],
            ['label' => $label, 'created_at' => now(), 'updated_at' => now()]
        ));

        $admin = User::query()->updateOrCreate([
            'email' => 'admin@agape153.com',
        ], [
            'name' => 'Agape153 Admin',
            'password' => 'password',
            'phone' => '+62816795153',
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
        $permissionIds = DB::table('permissions')->pluck('id', 'name');
        DB::table('role_user')->updateOrInsert(['role_id' => $roleIds['admin'], 'user_id' => $admin->id]);
        DB::table('role_user')->updateOrInsert(['role_id' => $roleIds['member'], 'user_id' => $member->id]);

        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleIds['admin']]);
        }

        $categoryData = [
            ['name' => 'Spices', 'type' => 'spice', 'description' => 'Nutmeg, mace, cloves, pepper, dried chili, chili powder, garlic, and selected Indonesian spices.', 'image_url' => $this->catalogImageUrl('Spices', 'Spices'), 'sort_order' => 1],
            ['name' => 'Herbal Roots', 'type' => 'spice', 'description' => 'Turmeric, galangal, curcuma xanthorrhiza, and Indonesian herbal roots prepared for sourcing inquiries.', 'image_url' => $this->catalogImageUrl('Herbal Roots', 'Herbal Roots'), 'sort_order' => 2],
            ['name' => 'Other Agricultural Products', 'type' => 'other', 'description' => 'Papaya leaves, banana stem, and other agriculture-derived products available by inquiry.', 'image_url' => $this->catalogImageUrl('Other Agricultural Products', 'Agriculture'), 'sort_order' => 3],
            ['name' => 'Coffee (Green Beans)', 'type' => 'coffee', 'description' => 'Robusta and arabica green beans from Indonesian origins for roasters, distributors, and international buyers.', 'image_url' => $this->catalogImageUrl('Coffee Green Beans', 'Coffee'), 'sort_order' => 4],
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

        foreach (['Robusta', 'Arabica'] as $index => $name) {
            $categories[$name] = Category::query()->updateOrCreate(
                ['slug' => 'green-beans-'.Str::slug($name)],
                [
                    'parent_id' => $categories['Coffee (Green Beans)']->id,
                    'name' => $name,
                    'type' => 'coffee',
                    'description' => "{$name} green beans for Agape153 coffee sourcing catalog.",
                    'image_url' => $this->catalogImageUrl("{$name} Green Beans", 'Coffee (Green Beans)'),
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

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
            ['category' => 'Spices', 'name' => 'Nutmeg / Pala (Whole & Mace)', 'sku' => 'SP-NUTMEG-MACE', 'short_description' => 'Whole nutmeg and mace from Indonesia for spice processors, horeca, retail, and export inquiries.', 'origin' => 'Indonesia', 'grade' => 'Whole & Mace', 'price' => 118000, 'featured' => true, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Whole nutmeg and mace'], ['label' => 'Use Case', 'value' => 'Spice blend, beverage, bakery, and food manufacturing'], ['label' => 'Packaging', 'value' => 'Retail pack, bulk sack, or buyer specification']]],
            ['category' => 'Spices', 'name' => 'Cloves / Cengkeh', 'sku' => 'SP-CLOVE', 'short_description' => 'Aromatic Indonesian cloves with strong fragrance for seasoning, beverage, and industrial needs.', 'origin' => 'Indonesia', 'grade' => 'Dry Whole', 'price' => 135000, 'featured' => true, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Dry whole cloves'], ['label' => 'Aroma', 'value' => 'Strong clove fragrance'], ['label' => 'Packaging', 'value' => 'Bulk or private label by request']]],
            ['category' => 'Spices', 'name' => 'White Pepper', 'sku' => 'SP-WHITE-PEPPER', 'short_description' => 'Clean white pepper with sharp aroma for retail, horeca, seasoning, and export buyers.', 'origin' => 'Indonesia', 'grade' => 'Cleaned', 'price' => 126000, 'featured' => true, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Whole white peppercorn'], ['label' => 'Quality', 'value' => 'Cleaned and sorted'], ['label' => 'Buyer Use', 'value' => 'Seasoning, food service, and spice processing']]],
            ['category' => 'Spices', 'name' => 'Black Pepper', 'sku' => 'SP-BLACK-PEPPER', 'short_description' => 'Indonesian black pepper with bold heat and warm spice profile for sourcing orders.', 'origin' => 'Indonesia', 'grade' => 'Cleaned', 'price' => 98000, 'featured' => true, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Whole black peppercorn'], ['label' => 'Profile', 'value' => 'Bold heat and warm spice aroma'], ['label' => 'Packaging', 'value' => 'Food grade sack or buyer request']]],
            ['category' => 'Spices', 'name' => 'Dried Chili / Cabe Kering', 'sku' => 'SP-DRIED-CHILI', 'short_description' => 'Dried chili for food service, seasoning, chili processing, and ingredient sourcing.', 'origin' => 'Indonesia', 'grade' => 'Dry Whole', 'price' => 65000, 'featured' => false, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Whole dried chili'], ['label' => 'Use Case', 'value' => 'Sauce, seasoning, and food manufacturing'], ['label' => 'Packaging', 'value' => 'Bulk packaging by request']]],
            ['category' => 'Spices', 'name' => 'Chili Powder / Cabe Bubuk', 'sku' => 'SP-CHILI-POWDER', 'short_description' => 'Ground chili powder for seasoning, packaged food, horeca, and private label supply.', 'origin' => 'Indonesia', 'grade' => 'Powder', 'price' => 78000, 'featured' => false, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Fine chili powder'], ['label' => 'Processing', 'value' => 'Ground from selected dried chili'], ['label' => 'Packaging', 'value' => 'Retail pack or bulk pack']]],
            ['category' => 'Spices', 'name' => 'Garlic / Bawang Putih', 'sku' => 'SP-GARLIC', 'short_description' => 'Garlic supply for retail, horeca, culinary production, and distribution buyers.', 'origin' => 'Indonesia', 'grade' => 'Whole', 'price' => 42000, 'featured' => false, 'export' => false, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Whole garlic'], ['label' => 'Use Case', 'value' => 'Culinary, horeca, and distribution'], ['label' => 'Supply', 'value' => 'Availability follows harvest and market condition']]],
            ['category' => 'Herbal Roots', 'name' => 'Turmeric / Kunyit', 'sku' => 'HR-TURMERIC', 'short_description' => 'Turmeric for herbal, spice, food color, beverage, and ingredient processing needs.', 'origin' => 'Indonesia', 'grade' => 'Fresh / Dry by Request', 'price' => 52000, 'featured' => false, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Fresh, sliced dry, or powder by request'], ['label' => 'Color', 'value' => 'Strong natural yellow-orange tone'], ['label' => 'Use Case', 'value' => 'Herbal drink, spice blend, and food ingredient']]],
            ['category' => 'Herbal Roots', 'name' => 'Galangal / Lengkuas', 'sku' => 'HR-GALANGAL', 'short_description' => 'Galangal root with aromatic character for culinary, herbal, and spice industry buyers.', 'origin' => 'Indonesia', 'grade' => 'Fresh / Dry by Request', 'price' => 48000, 'featured' => false, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Fresh root or dried slice by request'], ['label' => 'Profile', 'value' => 'Aromatic, warm, and earthy'], ['label' => 'Packaging', 'value' => 'Bulk packaging by buyer specification']]],
            ['category' => 'Herbal Roots', 'name' => 'Curcuma Xanthorrhiza / Temulawak', 'sku' => 'HR-TEMULAWAK', 'short_description' => 'Temulawak root for herbal drink, supplement ingredient, and traditional product sourcing.', 'origin' => 'Indonesia', 'grade' => 'Fresh / Dry by Request', 'price' => 50000, 'featured' => false, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Fresh root, dry slice, or powder by request'], ['label' => 'Use Case', 'value' => 'Herbal beverage and ingredient processing'], ['label' => 'Origin', 'value' => 'Indonesia']]],
            ['category' => 'Other Agricultural Products', 'name' => 'Papaya Leaves / Daun Papaya', 'sku' => 'AG-PAPAYA-LEAVES', 'short_description' => 'Papaya leaves available for agriculture-derived product inquiries and buyer-specific processing.', 'origin' => 'Indonesia', 'grade' => 'Fresh / Dry by Request', 'price' => 30000, 'featured' => false, 'export' => false, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Fresh or dried leaves by request'], ['label' => 'Use Case', 'value' => 'Agricultural processing and custom sourcing'], ['label' => 'Availability', 'value' => 'Confirmed per order volume']]],
            ['category' => 'Other Agricultural Products', 'name' => 'Banana Stem / Batang Pisang', 'sku' => 'AG-BANANA-STEM', 'short_description' => 'Banana stem for agricultural product inquiries, processing needs, and custom sourcing.', 'origin' => 'Indonesia', 'grade' => 'Fresh Cut', 'price' => 25000, 'featured' => false, 'export' => false, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Fresh cut banana stem'], ['label' => 'Use Case', 'value' => 'Agriculture-derived product and processing inquiry'], ['label' => 'Supply', 'value' => 'Prepared by confirmed buyer requirement']]],
            ['category' => 'Robusta', 'name' => 'Robusta Green Beans', 'sku' => 'CF-ROBUSTA-GREEN', 'short_description' => 'Indonesian robusta green beans for roasters, espresso blends, retail, and commercial buyers.', 'origin' => 'Indonesia', 'grade' => 'Green Beans', 'price' => 80000, 'featured' => true, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Green coffee beans'], ['label' => 'Profile', 'value' => 'Strong body and commercial roasting flexibility'], ['label' => 'Buyer Use', 'value' => 'Roasting, espresso blend, and distribution']]],
            ['category' => 'Arabica', 'name' => 'Arabica Green Beans', 'sku' => 'CF-ARABICA-GREEN', 'short_description' => 'Indonesian arabica green beans for specialty roasters, distributors, and international buyers.', 'origin' => 'Indonesia', 'grade' => 'Green Beans', 'price' => 145000, 'featured' => true, 'export' => true, 'catalog_media' => true, 'details' => [['label' => 'Form', 'value' => 'Green coffee beans'], ['label' => 'Profile', 'value' => 'Origin-dependent aroma, acidity, and sweetness'], ['label' => 'Buyer Use', 'value' => 'Specialty roasting, retail, and export']]],
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
            $payload = [
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
                'image_url' => $product['image'] ?? $this->catalogImageUrl($product['name'], $product['category']),
                'meta_title' => $product['name'].' - Agape153',
                'meta_description' => $product['short_description'],
            ];

            if ($product['catalog_media'] ?? false) {
                $payload['video_url'] = $this->catalogVideoUrl($product['name'], $product['category']);
            }

            if (array_key_exists('details', $product)) {
                $payload['product_details'] = $product['details'];
            }

            Product::query()->updateOrCreate(
                ['slug' => Str::slug($product['name'])],
                $payload
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
            'phone_number' => '+62816795153',
            'whatsapp_number' => '+62816795153',
            'company_email' => 'info.agape153@gmail.com',
            'company_address' => 'Indonesia',
            'youtube_url' => 'https://www.youtube.com/@AGAPE153CHANNEL',
            'instagram_handle' => '@agape153.official',
            'instagram_url' => 'https://www.instagram.com/agape153.official',
            'facebook_url' => 'https://www.facebook.com/profile.php?id=61590494259264',
            'linkedin_url' => 'https://www.linkedin.com/in/agape153',
            'tiktok_url' => 'https://www.tiktok.com/@agape153.official',
            'threads_handle' => '@agape153.official',
            'threads_url' => 'https://www.threads.net/@agape153.official',
            'seo_title' => 'Agape153 - Indonesian Spices & Coffee Export',
            'seo_description' => 'Supplier rempah-rempah dan kopi Indonesia untuk lokal dan ekspor.',
            'appearance_primary_color' => '#0f766e',
            'appearance_accent_color' => '#e9c95a',
            'appearance_soft_color' => '#edf7f4',
            'appearance_homepage_layout' => 'classic',
            'appearance_hero_badge' => 'Indonesian spices and coffee supplier',
            'appearance_hero_title' => 'Agape153',
            'appearance_hero_subtitle' => 'Katalog rempah-rempah, kopi arabica, dan robusta Indonesia untuk pembeli lokal, retail, horeca, distributor, dan importir internasional.',
            'appearance_hero_image_url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=900&q=80',
            'appearance_hero_slide_2_url' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=1800&q=80',
            'appearance_hero_slide_3_url' => 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=1800&q=80',
            'appearance_show_gallery' => '1',
            'appearance_show_testimonials' => '1',
            'footer_description' => 'Supplier rempah-rempah dan kopi Indonesia untuk pembeli lokal, distributor, horeca, dan importir global.',
            'high_value_order_threshold' => '10000000',
        ] as $key => $value) {
            DB::table('website_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'group' => str_starts_with($key, 'appearance_') ? 'appearance' : (str_starts_with($key, 'google_') ? 'integrations' : 'general'), 'created_at' => now(), 'updated_at' => now()]
            );
        }

        foreach ([
            'google_client_id' => '',
            'google_redirect_uri' => url('/auth/google/callback'),
        ] as $key => $value) {
            if (! DB::table('website_settings')->where('key', $key)->exists()) {
                DB::table('website_settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'group' => 'integrations',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function catalogImageUrl(string $title, string $group): string
    {
        $slug = Str::slug($title);
        $relativePath = "images/catalog/{$slug}.jpg";
        $absolutePath = public_path($relativePath);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0777, true);
        }

        if (! file_exists($absolutePath)) {
            $this->generateCatalogImage($absolutePath, $title, $group);
        }

        return '/'.$relativePath;
    }

    private function catalogVideoUrl(string $title, string $group): string
    {
        $slug = Str::slug($title);
        $relativePath = "videos/catalog/{$slug}.html";
        $absolutePath = public_path($relativePath);
        $imageUrl = $this->catalogImageUrl($title, $group);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0777, true);
        }

        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $safeGroup = htmlspecialchars($group, ENT_QUOTES, 'UTF-8');
        $safeImage = htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8');

        file_put_contents($absolutePath, <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$safeTitle} - Agape153 Video</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; overflow: hidden; background: #101820; color: #fff; font-family: Arial, sans-serif; }
        .stage { position: relative; display: grid; min-height: 100vh; place-items: center; isolation: isolate; }
        .stage::before { content: ""; position: absolute; inset: -20%; background: radial-gradient(circle at 20% 30%, rgba(233, 201, 90, .36), transparent 30%), radial-gradient(circle at 80% 70%, rgba(45, 157, 183, .34), transparent 32%); animation: pulse 7s ease-in-out infinite alternate; z-index: -2; }
        .stage::after { content: ""; position: absolute; inset: 0; background: linear-gradient(110deg, rgba(16, 24, 32, .15), rgba(16, 24, 32, .82)); z-index: -1; }
        img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .46; transform: scale(1.08); animation: drift 12s ease-in-out infinite alternate; z-index: -3; }
        .card { width: min(82vw, 760px); border: 1px solid rgba(255,255,255,.18); background: rgba(16,24,32,.72); padding: clamp(24px, 5vw, 56px); box-shadow: 0 28px 80px rgba(0,0,0,.38); backdrop-filter: blur(14px); }
        .stripe { display: grid; grid-template-columns: 1fr 1fr 1fr; height: 8px; margin-bottom: 28px; }
        .stripe span:nth-child(1) { background: #e64b3c; }
        .stripe span:nth-child(2) { background: #e9c95a; }
        .stripe span:nth-child(3) { background: #2d9db7; }
        p { margin: 0; color: #e9c95a; font-size: 14px; font-weight: 900; letter-spacing: .24em; text-transform: uppercase; }
        h1 { margin: 14px 0 0; font-size: clamp(36px, 8vw, 84px); line-height: .94; letter-spacing: 0; }
        strong { display: inline-flex; margin-top: 28px; border: 1px solid rgba(255,255,255,.18); padding: 12px 16px; color: #d5fff7; font-size: 14px; letter-spacing: .18em; text-transform: uppercase; }
        @keyframes drift { from { transform: scale(1.08) translate3d(-1.5%, -1%, 0); } to { transform: scale(1.18) translate3d(1.5%, 1%, 0); } }
        @keyframes pulse { from { transform: translate3d(-2%, -1%, 0) scale(1); opacity: .74; } to { transform: translate3d(2%, 1%, 0) scale(1.08); opacity: 1; } }
    </style>
</head>
<body>
    <main class="stage">
        <img src="{$safeImage}" alt="{$safeTitle}">
        <section class="card">
            <div class="stripe"><span></span><span></span><span></span></div>
            <p>Agape153 Catalog</p>
            <h1>{$safeTitle}</h1>
            <strong>{$safeGroup}</strong>
        </section>
    </main>
</body>
</html>
HTML);

        return '/'.$relativePath;
    }

    private function generateCatalogImage(string $path, string $title, string $group): void
    {
        $palette = $this->catalogPalette($group);
        $width = 1200;
        $height = 900;
        $image = imagecreatetruecolor($width, $height);
        imageantialias($image, true);

        $background = imagecolorallocate($image, $palette[0][0], $palette[0][1], $palette[0][2]);
        imagefilledrectangle($image, 0, 0, $width, $height, $background);

        for ($y = 0; $y < $height; $y += 6) {
            $ratio = $y / $height;
            $r = (int) ($palette[0][0] + (($palette[1][0] - $palette[0][0]) * $ratio));
            $g = (int) ($palette[0][1] + (($palette[1][1] - $palette[0][1]) * $ratio));
            $b = (int) ($palette[0][2] + (($palette[1][2] - $palette[0][2]) * $ratio));
            imagefilledrectangle($image, 0, $y, $width, $y + 6, imagecolorallocate($image, $r, $g, $b));
        }

        $white = imagecolorallocate($image, 255, 255, 255);
        $ink = imagecolorallocate($image, 16, 24, 32);
        $accent = imagecolorallocate($image, 233, 201, 90);
        $red = imagecolorallocate($image, 230, 75, 60);
        $blue = imagecolorallocate($image, 45, 157, 183);
        $dotColor = imagecolorallocate($image, $palette[2][0], $palette[2][1], $palette[2][2]);

        imagefilledellipse($image, 980, 110, 360, 360, imagecolorallocate($image, min(255, $palette[1][0] + 24), min(255, $palette[1][1] + 24), min(255, $palette[1][2] + 24)));
        imagefilledellipse($image, 140, 760, 460, 460, imagecolorallocate($image, max(0, $palette[0][0] - 28), max(0, $palette[0][1] - 28), max(0, $palette[0][2] - 28)));
        imagefilledrectangle($image, 80, 86, 1120, 814, imagecolorallocate($image, 248, 250, 249));
        imagefilledrectangle($image, 80, 86, 1120, 104, $red);
        imagefilledrectangle($image, 426, 86, 774, 104, $accent);
        imagefilledrectangle($image, 774, 86, 1120, 104, $blue);

        $this->drawWrappedText($image, strtoupper($group), 130, 170, 5, $blue, 46);
        $this->drawWrappedText($image, $title, 130, 280, 5, $ink, 30);

        foreach (range(0, 7) as $index) {
            $x = 160 + ($index * 120);
            $y = 640 + (($index % 2) * 34);
            imagefilledellipse($image, $x, $y, 86, 86, $dotColor);
            imageellipse($image, $x, $y, 88, 88, $ink);
        }

        imagestring($image, 5, 130, 730, 'AGAPE153 INDONESIAN COMMODITY CATALOG', $ink);
        imagestring($image, 4, 130, 760, 'Photo placeholder can be replaced from Admin Products.', imagecolorallocate($image, 71, 85, 105));

        imagejpeg($image, $path, 88);
        imagedestroy($image);
    }

    /**
     * @return array<int, array<int, int>>
     */
    private function catalogPalette(string $group): array
    {
        $lower = Str::lower($group);

        if (str_contains($lower, 'coffee') || str_contains($lower, 'robusta') || str_contains($lower, 'arabica')) {
            return [[67, 49, 38], [185, 143, 88], [139, 95, 61]];
        }

        if (str_contains($lower, 'herbal')) {
            return [[35, 89, 68], [223, 178, 66], [224, 133, 46]];
        }

        if (str_contains($lower, 'agricultural')) {
            return [[38, 95, 81], [139, 196, 128], [110, 168, 98]];
        }

        return [[91, 45, 37], [214, 94, 59], [174, 67, 48]];
    }

    private function drawWrappedText($image, string $text, int $x, int $y, int $font, int $color, int $maxChars): void
    {
        foreach (explode("\n", wordwrap($text, $maxChars)) as $index => $line) {
            imagestring($image, $font, $x, $y + ($index * 38), $line, $color);
        }
    }
}
