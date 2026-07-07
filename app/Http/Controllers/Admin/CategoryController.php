<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::query()->with('parent')->orderBy('sort_order')->orderBy('name')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.categories.form', [
            'category' => new Category(['is_active' => true]),
            'parents' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Category::query()->create($this->validated($request));

        return redirect()->route('admin.categories.index')->with('status', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parents' => Category::query()->whereKeyNot($category->id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('admin.categories.index')->with('status', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('status', 'Kategori dihapus.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140', 'unique:categories,slug,'.($category?->id ?? 'NULL')],
            'type' => ['required', 'in:spice,coffee,export,other'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
