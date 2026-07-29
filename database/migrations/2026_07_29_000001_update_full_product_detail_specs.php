<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
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

    public function down(): void
    {
        //
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
                'Product Name' => 'White Pepper',
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
                'Product Name' => 'Black Pepper',
                'Origin' => 'Indonesia',
                'Quality' => 'Export Grade',
                'Form' => 'Whole Dried Peppercorns',
                'Color' => 'Black',
                'Moisture' => 'Max. 13%',
                'Purity' => 'Min. 99%',
                'Packaging' => '25 kg or 50 kg PP Bags (Custom packaging available)',
            ]);
        }

        if (str_contains($name, 'nutmeg') || str_contains($name, 'pala')) {
            return $this->rows([
                'Product Name' => 'Whole Nutmeg',
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
                'Product Name' => 'Dried Ginger',
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
                'Product Name' => 'Curcuma (Java Turmeric)',
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
                'Product Name' => 'Turmeric',
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
                'Product Name' => 'Indonesian Coffee Beans',
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
            'Product Name' => $product,
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
