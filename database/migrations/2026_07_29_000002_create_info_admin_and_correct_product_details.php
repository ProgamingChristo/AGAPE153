<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createInfoAdminAccount();
        $this->upsertSetting('phone_display', '+62816 795 153');
        $this->correctProductDetails();
    }

    public function down(): void
    {
        //
    }

    private function createInfoAdminAccount(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        DB::table('users')->updateOrInsert(
            ['email' => 'info@agape153.com'],
            [
                'name' => 'Agape153 Info',
                'password' => Hash::make('password'),
                'phone' => '+62816795153',
                'company_name' => 'Agape153',
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if (! Schema::hasTable('roles') || ! Schema::hasTable('role_user')) {
            return;
        }

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $infoUserId = DB::table('users')->where('email', 'info@agape153.com')->value('id');

        if ($adminRoleId && $infoUserId) {
            DB::table('role_user')->updateOrInsert([
                'role_id' => $adminRoleId,
                'user_id' => $infoUserId,
            ]);
        }
    }

    private function upsertSetting(string $key, string $value): void
    {
        if (! Schema::hasTable('website_settings')) {
            return;
        }

        DB::table('website_settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => $value,
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function correctProductDetails(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'product_details')) {
            return;
        }

        DB::table('products')
            ->select(['id', 'name', 'origin'])
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'product_details' => json_encode($this->detailsFor($product->name, $product->origin ?: 'Indonesia')),
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    private function detailsFor(string $product, string $origin): array
    {
        $name = strtolower($product);
        $origin = $origin ?: 'Indonesia';

        if (str_contains($name, 'white pepper') || str_contains($name, 'lada putih')) {
            return $this->rows([
                'Item Name' => 'White Pepper',
                'Origin' => 'Indonesia',
                'Quality' => 'Export Grade',
                'Form' => 'Whole Dried Peppercorns',
                'Color' => 'Creamy White',
                'Moisture' => 'Max. 13%',
                'Purity' => 'Min. 99%',
                'Packaging' => '25 kg or 50 kg PP Bags (Custom packaging available)',
            ]);
        }

        if (str_contains($name, 'black pepper') || str_contains($name, 'lada hitam')) {
            return $this->rows([
                'Item Name' => 'Black Pepper',
                'Origin' => 'Indonesia',
                'Quality' => 'Export Grade',
                'Form' => 'Whole Dried Peppercorns',
                'Color' => 'Black',
                'Moisture' => 'Max. 13%',
                'Purity' => 'Min. 99%',
                'Packaging' => '25 kg or 50 kg PP Bags (Custom packaging available)',
            ]);
        }

        if (str_contains($name, 'clove') || str_contains($name, 'cengkeh')) {
            return $this->rows([
                'Item Name' => 'Cloves / Cengkeh',
                'Origin' => 'Indonesia',
                'Quality' => 'Export Grade',
                'Form' => 'Whole Dried Cloves',
                'Moisture' => 'Max. 12%',
                'Packaging' => '25 kg or 50 kg PP Bags (Custom packaging available)',
            ]);
        }

        if (str_contains($name, 'nutmeg') || str_contains($name, 'pala')) {
            return $this->rows([
                'Item Name' => 'Whole Nutmeg',
                'Origin' => 'Indonesia',
                'Quality' => 'Export Grade',
                'Form' => 'Whole Dried Nutmeg',
                'Moisture' => 'Max. 10%',
                'Color' => 'Natural Brown',
                'Packaging' => '25 kg or 50 kg Bags',
            ]);
        }

        if (str_contains($name, 'ginger') || str_contains($name, 'jahe')) {
            return $this->rows([
                'Item Name' => 'Dried Ginger',
                'Origin' => 'Indonesia',
                'Quality' => 'Export Grade',
                'Form' => 'Whole, Sliced, or Powder',
                'Moisture' => 'Max. 12%',
                'Color' => 'Natural Light Brown',
                'Packaging' => '25 kg PP Bags or as requested',
            ]);
        }

        if (str_contains($name, 'curcuma') || str_contains($name, 'temulawak')) {
            return $this->rows([
                'Item Name' => 'Curcuma (Java Turmeric)',
                'Origin' => 'Indonesia',
                'Quality' => 'Export Grade',
                'Form' => 'Dried Whole, Sliced, or Powder',
                'Moisture' => 'Max. 12%',
                'Color' => 'Yellow-Orange',
                'Packaging' => '25 kg PP Bags',
            ]);
        }

        if (str_contains($name, 'turmeric') || str_contains($name, 'kunyit')) {
            return $this->rows([
                'Item Name' => 'Turmeric',
                'Origin' => 'Indonesia',
                'Quality' => 'Export Grade',
                'Form' => 'Whole Dried Fingers, Sliced, or Powder',
                'Moisture' => 'Max. 12%',
                'Color' => 'Bright Yellow',
                'Packaging' => '25 kg PP Bags or Customized',
            ]);
        }

        if (str_contains($name, 'coffee') || str_contains($name, 'robusta') || str_contains($name, 'arabica')) {
            return $this->rows([
                'Item Name' => 'Indonesian Coffee Beans',
                'Origin' => 'Indonesia',
                'Variety' => 'Arabica and Robusta',
                'Quality' => 'Export Grade',
                'Process' => 'Natural, Washed, or Semi-Washed',
                'Form' => 'Green Coffee Beans',
                'Moisture' => '11-13%',
                'Packaging' => '60 kg Jute Bags or Customized',
            ]);
        }

        return $this->rows([
            'Item Name' => $product,
            'Origin' => $origin,
            'Quality' => 'Export Grade',
            'Form' => 'By request',
            'Moisture' => 'Max. 13%',
            'Packaging' => '25 kg PP Bags or Customized',
        ]);
    }

    /**
     * @param  array<string, string>  $details
     * @return array<int, array{label: string, value: string}>
     */
    private function rows(array $details): array
    {
        $rows = [];

        foreach ($details as $label => $value) {
            $rows[] = ['label' => $label, 'value' => $value];
        }

        return $rows;
    }
};
