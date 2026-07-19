<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductImportExportController extends Controller
{
    public function export()
    {
        $products = Product::query()->with('category')->orderBy('name')->get();

        return response()->streamDownload(function () use ($products): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'slug', 'category', 'sku', 'unit', 'price', 'currency', 'stock_quantity', 'is_active', 'is_featured', 'export_ready', 'short_description', 'image_url', 'video_url']);

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->name,
                    $product->slug,
                    $product->category?->name,
                    $product->sku,
                    $product->unit,
                    $product->price,
                    $product->currency,
                    $product->stock_quantity,
                    $product->is_active ? 1 : 0,
                    $product->is_featured ? 1 : 0,
                    $product->export_ready ? 1 : 0,
                    $product->short_description,
                    $product->image_url,
                    $product->video_url,
                ]);
            }

            fclose($handle);
        }, 'agape153-products.csv', ['Content-Type' => 'text/csv']);
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:4096'],
        ]);

        $file = fopen($data['csv_file']->getRealPath(), 'r');
        $header = fgetcsv($file);
        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            $record = array_combine($header, $row);

            if (! $record || blank($record['name'] ?? null)) {
                continue;
            }

            $category = Category::query()->firstOrCreate(
                ['slug' => Str::slug($record['category'] ?: 'Uncategorized')],
                ['name' => $record['category'] ?: 'Uncategorized', 'type' => 'other', 'is_active' => true]
            );

            Product::query()->updateOrCreate(
                ['slug' => $record['slug'] ?: Str::slug($record['name'])],
                [
                    'category_id' => $category->id,
                    'name' => $record['name'],
                    'sku' => $record['sku'] ?: null,
                    'unit' => $record['unit'] ?: 'Kg',
                    'price' => (float) ($record['price'] ?: 0),
                    'currency' => $record['currency'] ?: 'IDR',
                    'stock_quantity' => (int) ($record['stock_quantity'] ?: 0),
                    'is_active' => (bool) ($record['is_active'] ?? true),
                    'is_featured' => (bool) ($record['is_featured'] ?? false),
                    'export_ready' => (bool) ($record['export_ready'] ?? false),
                    'short_description' => $record['short_description'] ?? null,
                    'image_url' => $record['image_url'] ?? null,
                    'video_url' => $record['video_url'] ?? null,
                ]
            );
            $count++;
        }

        fclose($file);

        return back()->with('status', "{$count} products imported/updated.");
    }
}
