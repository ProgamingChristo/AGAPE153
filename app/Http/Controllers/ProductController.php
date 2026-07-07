<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with('category')
            ->active()
            ->search($request->string('q')->toString())
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category')));
            })
            ->when($request->boolean('export_ready'), fn ($query) => $query->where('export_ready', true))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::query()->active()->orderBy('name')->get(),
            'selectedCategory' => $request->string('category')->toString(),
            'query' => $request->string('q')->toString(),
        ]);
    }

    public function show(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->increment('view_count');

        ProductView::query()->create([
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'device' => str($request->userAgent() ?? 'unknown')->contains('Mobile') ? 'mobile' : 'desktop',
            'browser' => str($request->userAgent() ?? 'unknown')->limit(120)->toString(),
        ]);

        return view('products.show', [
            'product' => $product->load(['category', 'images']),
            'relatedProducts' => Product::query()
                ->active()
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->take(4)
                ->get(),
        ]);
    }
}
