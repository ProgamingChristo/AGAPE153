<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create()
    {
        [$lines, $subtotal] = $this->cartLines();

        if ($lines->isEmpty()) {
            return redirect()->route('products.index')->with('status', 'Silakan pilih produk terlebih dahulu.');
        }

        return view('checkout.create', compact('lines', 'subtotal'));
    }

    public function store(Request $request)
    {
        [$lines, $subtotal] = $this->cartLines();

        if ($lines->isEmpty()) {
            return redirect()->route('products.index')->with('status', 'Cart masih kosong.');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['nullable', 'email', 'max:160'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'country' => ['required', 'string', 'max:80'],
            'shipping_address' => ['required', 'string', 'max:1200'],
            'notes' => ['nullable', 'string', 'max:1200'],
        ]);

        $order = DB::transaction(function () use ($request, $data, $lines, $subtotal) {
            $order = Order::query()->create([
                ...$data,
                'order_number' => 'AGP-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'user_id' => $request->user()?->id,
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'total_amount' => $subtotal,
                'tracking_code' => 'TRK'.Str::upper(Str::random(9)),
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $line['quantity'],
                    'unit' => $product->unit,
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['line_total'],
                ]);
            }

            $phone = preg_replace('/\D+/', '', WebsiteSetting::value('whatsapp_number', '6281234567890'));
            $message = "Halo Agape153, saya ingin konfirmasi order {$order->order_number} atas nama {$order->customer_name}.";
            $order->update(['wa_checkout_url' => "https://wa.me/{$phone}?text=".rawurlencode($message)]);

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('checkout.success', $order)->with('status', 'Order berhasil dibuat.');
    }

    public function success(Order $order)
    {
        return view('checkout.success', ['order' => $order->load('items')]);
    }

    public function trackForm()
    {
        return view('orders.track');
    }

    public function track(Request $request)
    {
        $data = $request->validate([
            'keyword' => ['required', 'string', 'max:80'],
        ]);

        $order = Order::query()
            ->with('items')
            ->where('order_number', $data['keyword'])
            ->orWhere('tracking_code', $data['keyword'])
            ->first();

        return view('orders.track', compact('order'));
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
