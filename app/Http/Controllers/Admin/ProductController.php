<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::query()->with('category')->latest()->paginate(15),
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

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:products,slug,'.($product?->id ?? 'NULL')],
            'sku' => ['nullable', 'string', 'max:80', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'short_description' => ['nullable', 'string', 'max:600'],
            'description' => ['nullable', 'string'],
            'origin' => ['nullable', 'string', 'max:120'],
            'grade' => ['nullable', 'string', 'max:120'],
            'unit' => ['required', 'string', 'max:20'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'min_order_quantity' => ['required', 'integer', 'min:1'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:500'],
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

        return $data;
    }
}
