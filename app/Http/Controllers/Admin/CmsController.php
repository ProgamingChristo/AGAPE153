<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentSection;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\NewsPost;
use App\Models\Testimonial;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    public function index()
    {
        return view('admin.cms.index', [
            'sections' => ContentSection::query()->orderBy('page')->orderBy('sort_order')->get(),
            'faqs' => Faq::query()->orderBy('sort_order')->get(),
            'galleries' => Gallery::query()->latest()->take(20)->get(),
            'testimonials' => Testimonial::query()->latest()->take(20)->get(),
            'newsPosts' => NewsPost::query()->latest()->take(20)->get(),
            'footerDescription' => WebsiteSetting::value('footer_description', 'Supplier rempah-rempah dan kopi Indonesia untuk pembeli lokal, distributor, horeca, dan importir international.'),
        ]);
    }

    public function storeSection(Request $request)
    {
        $data = $this->sectionData($request);
        $data['key'] = $data['key'] ?: Str::slug($data['page'].'-'.$data['title'].'-'.Str::random(4));

        ContentSection::query()->create($data);

        return back()->with('status', 'CMS section created.');
    }

    public function updateSection(Request $request, ContentSection $section)
    {
        $section->update($this->sectionData($request));

        return back()->with('status', 'CMS section updated.');
    }

    public function storeFaq(Request $request)
    {
        Faq::query()->create($request->validate([
            'question' => ['required', 'string', 'max:200'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'FAQ created.');
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:200'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $faq->update($data);

        return back()->with('status', 'FAQ updated.');
    }

    public function storeGallery(Request $request)
    {
        Gallery::query()->create($this->mediaData($request, ['title' => ['required', 'string', 'max:160']]));

        return back()->with('status', 'Gallery item created.');
    }

    public function updateGallery(Request $request, Gallery $gallery)
    {
        $gallery->update($this->mediaData($request, ['title' => ['required', 'string', 'max:160']]));

        return back()->with('status', 'Gallery item updated.');
    }

    public function storeTestimonial(Request $request)
    {
        Testimonial::query()->create($this->testimonialData($request));

        return back()->with('status', 'Testimonial created.');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($this->testimonialData($request));

        return back()->with('status', 'Testimonial updated.');
    }

    public function storeNews(Request $request)
    {
        NewsPost::query()->create($this->newsData($request));

        return back()->with('status', 'News post created.');
    }

    public function updateNews(Request $request, NewsPost $newsPost)
    {
        $newsPost->update($this->newsData($request));

        return back()->with('status', 'News post updated.');
    }

    public function updateFooter(Request $request)
    {
        $data = $request->validate([
            'footer_description' => ['required', 'string', 'max:500'],
            'company_email' => ['required', 'email', 'max:160'],
            'company_secondary_email' => ['nullable', 'email', 'max:160'],
            'company_address' => ['nullable', 'string', 'max:250'],
            'high_value_order_threshold' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($data as $key => $value) {
            WebsiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'general']);
            Cache::forget("setting:{$key}");
        }

        return back()->with('status', 'Footer settings updated.');
    }

    private function sectionData(Request $request): array
    {
        $data = $request->validate([
            'key' => ['nullable', 'string', 'max:120'],
            'page' => ['required', 'string', 'max:80'],
            'title' => ['nullable', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:220'],
            'body' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }

    private function mediaData(Request $request, array $extraRules = []): array
    {
        $data = $request->validate($extraRules + [
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        unset($data['image_file']);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('cms', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $data['image_url'] = $data['image_url'] ?: asset('images/product-placeholder.svg');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function testimonialData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'country' => ['nullable', 'string', 'max:80'],
            'message' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function newsData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
