<?php

namespace App\Http\Controllers;

use App\Models\CartEvent;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        [$lines, $subtotal] = $this->cartLines();

        return view('cart.index', compact('lines', 'subtotal'));
    }

    public function store(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        $cart = session('cart', []);
        $id = (string) $product->id;
        $cart[$id] = [
            'product_id' => $product->id,
            'quantity' => ($cart[$id]['quantity'] ?? 0) + (int) $data['quantity'],
        ];

        session(['cart' => $cart]);

        CartEvent::query()->create([
            'session_id' => $request->session()->getId(),
            'user_id' => $request->user()?->id,
            'product_id' => $product->id,
            'event' => 'added',
            'quantity' => (int) $data['quantity'],
            'source' => $request->headers->get('referer'),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('status', "{$product->name} ditambahkan ke cart.");
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['integer', 'min:0', 'max:9999'],
        ]);

        $cart = session('cart', []);

        foreach ($data['quantities'] as $productId => $quantity) {
            if ((int) $quantity === 0) {
                unset($cart[$productId]);

                continue;
            }

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = (int) $quantity;
            }
        }

        session(['cart' => $cart]);

        CartEvent::query()->create([
            'session_id' => $request->session()->getId(),
            'user_id' => $request->user()?->id,
            'event' => 'updated',
            'quantity' => collect($cart)->sum('quantity'),
            'source' => $request->headers->get('referer'),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('status', 'Cart diperbarui.');
    }

    public function destroy(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[(string) $product->id]);
        session(['cart' => $cart]);

        return back()->with('status', 'Produk dihapus dari cart.');
    }

    private function cartLines(): array
    {
        $cart = session('cart', []);
        $products = Product::query()
            ->whereIn('id', collect($cart)->pluck('product_id')->all())
            ->get()
            ->keyBy('id');

        $lines = collect($cart)->map(function (array $item) use ($products) {
            $product = $products->get($item['product_id']);

            if (! $product) {
                return null;
            }

            $quantity = (int) $item['quantity'];
            $unitPrice = (float) ($product->price ?? 0);

            return [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * $quantity,
            ];
        })->filter()->values();

        return [$lines, $lines->sum('line_total')];
    }
}
