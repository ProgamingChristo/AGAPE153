<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContentSection;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\NewsPost;
use App\Models\Product;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'featuredProducts' => Product::query()
                ->select('products.*')
                ->join('categories as catalog_categories', 'catalog_categories.id', '=', 'products.category_id')
                ->with('category')
                ->active()
                ->featured()
                ->orderByRaw("CASE catalog_categories.type WHEN 'spice' THEN 1 WHEN 'other' THEN 2 WHEN 'export' THEN 3 WHEN 'coffee' THEN 4 ELSE 5 END")
                ->orderBy('catalog_categories.sort_order')
                ->orderBy('products.name')
                ->take(6)
                ->get(),
            'categories' => Category::query()
                ->active()
                ->whereNull('parent_id')
                ->with(['children' => fn ($query) => $query->active()])
                ->orderBy('sort_order')
                ->take(8)
                ->get(),
            'contentSections' => ContentSection::query()
                ->where('page', 'home')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'galleries' => Gallery::query()->where('is_active', true)->latest()->take(6)->get(),
            'testimonials' => Testimonial::query()->where('is_active', true)->latest()->take(3)->get(),
            'faqs' => Faq::query()->where('is_active', true)->orderBy('sort_order')->take(8)->get(),
            'newsPosts' => NewsPost::query()->published()->latest('published_at')->take(3)->get(),
        ]);
    }
}
