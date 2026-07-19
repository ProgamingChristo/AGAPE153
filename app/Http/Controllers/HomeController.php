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
                ->with('category')
                ->active()
                ->featured()
                ->latest()
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
