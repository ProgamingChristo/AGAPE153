<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_returns_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_catalog_and_product_detail_return_successfully(): void
    {
        $category = Category::query()->create([
            'name' => 'Rempah-rempah',
            'slug' => 'rempah-rempah',
            'type' => 'spice',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Lada Hitam',
            'slug' => 'lada-hitam',
            'short_description' => 'Lada hitam Indonesia.',
            'unit' => 'Kg',
            'price' => 98000,
            'currency' => 'IDR',
            'stock_quantity' => 10,
            'is_active' => true,
            'image_url' => 'https://example.com/lada.jpg',
        ]);

        $this->get('/products')->assertStatus(200)->assertSee('Lada Hitam');
        $this->get(route('products.show', $product))->assertStatus(200)->assertSee('Lada Hitam');
    }

    public function test_cart_can_receive_product(): void
    {
        $category = Category::query()->create([
            'name' => 'Robusta Coffee',
            'slug' => 'robusta-coffee',
            'type' => 'coffee',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Robusta Grade A Jambi',
            'slug' => 'robusta-grade-a-jambi',
            'unit' => 'Kg',
            'price' => 80000,
            'currency' => 'IDR',
            'stock_quantity' => 10,
            'is_active' => true,
            'image_url' => 'https://example.com/robusta.jpg',
        ]);

        $this->post(route('cart.store', $product), ['quantity' => 2])->assertSessionHas('cart');
        $this->get(route('cart.index'))->assertStatus(200)->assertSee('Robusta Grade A Jambi');
    }

    public function test_admin_area_requires_authentication(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }
}
