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
                            'product_details' => json_encode($this->details($product->name, $product->origin ?: 'Indonesia')),
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
    private function details(string $product, string $origin): array
    {
        return [
            ['label' => 'Product', 'value' => $product],
            ['label' => 'Origin', 'value' => $origin],
            ['label' => 'Quality', 'value' => 'Export Quality'],
            ['label' => 'Moisture Content', 'value' => 'Max. 13%'],
            ['label' => 'Packaging', 'value' => '20 kg PP Bag'],
        ];
    }
};
