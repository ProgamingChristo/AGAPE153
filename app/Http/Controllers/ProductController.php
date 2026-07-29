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
            ->select('products.*')
            ->join('categories as catalog_categories', 'catalog_categories.id', '=', 'products.category_id')
            ->with('category')
            ->withAvg(['reviews as reviews_avg_rating' => fn ($query) => $query->where('status', 'published')], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($query) => $query->where('status', 'published')])
            ->active()
            ->search($request->string('q')->toString())
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category')));
            })
            ->when($request->boolean('export_ready'), fn ($query) => $query->where('export_ready', true))
            ->orderByRaw("CASE catalog_categories.type WHEN 'spice' THEN 1 WHEN 'other' THEN 2 WHEN 'export' THEN 3 WHEN 'coffee' THEN 4 ELSE 5 END")
            ->orderBy('catalog_categories.sort_order')
            ->orderBy('products.name')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::query()
                ->active()
                ->orderByRaw("CASE type WHEN 'spice' THEN 1 WHEN 'other' THEN 2 WHEN 'export' THEN 3 WHEN 'coffee' THEN 4 ELSE 5 END")
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
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
            'product' => $product->load(['category', 'images', 'publishedReviews.user', 'publishedReviews.repliedBy']),
            'relatedProducts' => Product::query()
                ->active()
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->take(4)
                ->get(),
        ]);
    }
}
