<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')->update([
            'unit' => 'Kg',
            'min_order_quantity' => 100,
            'stock_quantity' => 20000,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('products')->update([
            'unit' => 'Kg',
            'min_order_quantity' => 1,
            'stock_quantity' => 1000,
            'updated_at' => now(),
        ]);
    }
};
