<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\StockMovement;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::query()->with('category')->latest()->paginate(15),
            'activeProducts' => Product::query()->where('is_active', true)->count(),
            'lowStockProducts' => Product::query()->where('stock_quantity', '<=', 5)->count(),
            'recentReviews' => ProductReview::query()
                ->with(['product', 'user', 'repliedBy'])
                ->latest()
                ->take(6)
                ->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product([
                'is_active' => true,
                'currency' => 'IDR',
                'unit' => 'Kg',
                'min_order_quantity' => 1,
            ]),
            'categories' => Category::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Product::query()->create($this->validated($request));

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('admin.products.index')->with('status', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('status', 'Produk dihapus.');
    }

    public function trash()
    {
        return view('admin.products.trash', [
            'products' => Product::onlyTrashed()->with('category')->latest('deleted_at')->paginate(15),
        ]);
    }

    public function restore(int $product)
    {
        $trashedProduct = Product::onlyTrashed()->findOrFail($product);
        $trashedProduct->restore();

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil direstore.');
    }

    public function updateStock(Request $request, Product $product, NotificationService $notifications)
    {
        $data = $request->validate([
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $before = (int) $product->stock_quantity;
        $after = (int) $data['stock_quantity'];

        $product->update(['stock_quantity' => $after]);

        StockMovement::query()->create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'delta' => $after - $before,
            'type' => 'adjustment',
            'reason' => $data['reason'] ?? 'Admin stock adjustment',
        ]);

        if ($after <= 5) {
            $notifications->notifyAdmin(
                'inventory.low_stock',
                'Low stock alert',
                "Low stock alert: {$product->name} now has {$after} {$product->unit}.",
                ['product_id' => $product->id, 'stock' => $after]
            );
        }

        return back()->with('status', "Stock {$product->name} diperbarui.");
    }

    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('status', $product->is_active ? 'Produk diaktifkan.' : 'Produk dinonaktifkan.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:products,slug,'.($product?->id ?? 'NULL')],
            'sku' => ['nullable', 'string', 'max:80', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'short_description' => ['nullable', 'string', 'max:600'],
            'description' => ['nullable', 'string'],
            'detail_labels' => ['nullable', 'array'],
            'detail_labels.*' => ['nullable', 'string', 'max:120'],
            'detail_values' => ['nullable', 'array'],
            'detail_values.*' => ['nullable', 'string', 'max:500'],
            'origin' => ['nullable', 'string', 'max:120'],
            'grade' => ['nullable', 'string', 'max:120'],
            'unit' => ['required', 'string', 'max:20'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'min_order_quantity' => ['required', 'integer', 'min:1'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:51200'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'export_ready' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['export_ready'] = $request->boolean('export_ready');
        $data['product_details'] = collect($request->input('detail_labels', []))
            ->map(function ($label, $index) use ($request): ?array {
                $label = trim((string) $label);
                $value = trim((string) $request->input("detail_values.{$index}", ''));

                if ($label === '' && $value === '') {
                    return null;
                }

                if ($value === '') {
                    return null;
                }

                return [
                    'label' => $label ?: 'Detail',
                    'value' => $value,
                ];
            })
            ->filter()
            ->values()
            ->all();

        unset($data['detail_labels'], $data['detail_values'], $data['image_file'], $data['video_file']);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $data['image_url'] = Storage::url($path);
        }

        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('products/videos', 'public');
            $data['video_url'] = Storage::url($path);
        }

        return $data;
    }
}
