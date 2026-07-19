<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\WebsiteSetting;
use App\Services\MidtransService;
use App\Services\NotificationService;
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

    public function store(Request $request, MidtransService $midtrans, NotificationService $notifications)
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
            'payment_method' => ['required', 'in:whatsapp,midtrans'],
        ]);

        $order = DB::transaction(function () use ($request, $data, $lines, $subtotal) {
            $approvalStatus = $subtotal >= (float) WebsiteSetting::value('high_value_order_threshold', '10000000')
                ? 'needs_review'
                : 'standard';

            $order = Order::query()->create([
                ...$data,
                'order_number' => 'AGP-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'user_id' => $request->user()?->id,
                'approval_status' => $approvalStatus,
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'discount_amount' => 0,
                'total_amount' => $subtotal,
                'tracking_code' => 'TRK'.Str::upper(Str::random(9)),
                'shipping_status' => 'order_created',
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'product_image_url' => $product->image_url,
                    'quantity' => $line['quantity'],
                    'unit' => $product->unit,
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['line_total'],
                ]);
            }

            $phone = preg_replace('/\D+/', '', WebsiteSetting::value('whatsapp_number', '+62816795153'));
            $message = "Halo Agape153, saya ingin konfirmasi order {$order->order_number} atas nama {$order->customer_name}.";
            $order->update(['wa_checkout_url' => "https://wa.me/{$phone}?text=".rawurlencode($message)]);

            return $order;
        });

        if ($order->payment_method === 'midtrans') {
            $midtrans->createSnapTransaction($order->load('items'));
        }

        $message = "New order {$order->order_number} from {$order->customer_name}. Total Rp".number_format((float) $order->total_amount, 0, ',', '.').'.';
        $notifications->notifyAdmin('order.created', 'New Agape153 order', $message, ['order_id' => $order->id]);
        $notifications->sendEmail('order.created', $order->customer_email, 'Agape153 order created', "Your order {$order->order_number} has been created.", ['order_id' => $order->id]);

        session()->forget('cart');

        if ($order->payment_method === 'whatsapp') {
            return redirect()->away($order->wa_checkout_url);
        }

        return redirect()
            ->route('checkout.success', ['order' => $order, 'pay' => 'midtrans'])
            ->with('status', 'Order berhasil dibuat. Popup pembayaran online akan muncul otomatis.');
    }

    public function success(Order $order, MidtransService $midtrans)
    {
        $this->authorizeOrderOwner($order);
        $order->load('items');
        $midtransError = null;

        if ($order->payment_method === 'midtrans' && $order->payment_status !== 'paid' && ! $order->midtrans_snap_token) {
            $midtrans->createSnapTransaction($order);
            $order->refresh()->load('items');
            $midtransError = $midtrans->lastError();
        }

        return view('checkout.success', [
            'order' => $order,
            'midtransClientKey' => config('services.midtrans.client_key'),
            'midtransIsProduction' => filter_var(config('services.midtrans.is_production'), FILTER_VALIDATE_BOOLEAN),
            'autoOpenMidtrans' => request()->query('pay') === 'midtrans',
            'midtransError' => $midtransError,
        ]);
    }

    public function payWithMidtrans(Order $order, MidtransService $midtrans)
    {
        $this->authorizeOrderOwner($order);
        abort_if($order->payment_status === 'paid', 422, 'Order already paid.');
        abort_unless($order->payment_method === 'midtrans', 404);

        $transaction = $midtrans->createSnapTransaction($order->load('items'));

        if (! $transaction) {
            return redirect()
                ->route('checkout.success', ['order' => $order, 'pay' => 'midtrans'])
                ->withErrors(['payment' => $midtrans->lastError() ?: 'Payment gateway belum bisa membuat token pembayaran.']);
        }

        return redirect()
            ->route('checkout.success', ['order' => $order, 'pay' => 'midtrans'])
            ->with('status', 'Popup pembayaran online akan muncul otomatis.');
    }

    public function midtransClientSuccess(Request $request, Order $order, MidtransService $midtrans, NotificationService $notifications)
    {
        $this->authorizeOrderOwner($order);
        abort_unless($order->payment_method === 'midtrans', 404);

        $data = $request->validate([
            'transaction_id' => ['nullable', 'string', 'max:120'],
            'transaction_status' => ['nullable', 'string', 'max:80'],
            'payment_type' => ['nullable', 'string', 'max:80'],
            'status_code' => ['nullable', 'string', 'max:20'],
        ]);

        $midtrans->applyPaymentStatus($order, $data, $notifications, 'client_success');
        $midtrans->syncOrderPaymentStatus($order->refresh(), $notifications, 'client_status_lookup');

        return response()->json([
            'redirect_url' => route('checkout.payment-success', $order),
        ]);
    }

    public function paymentSuccess(Order $order, MidtransService $midtrans, NotificationService $notifications)
    {
        $this->authorizeOrderOwner($order);

        $paymentSyncError = null;

        if ($order->payment_method === 'midtrans' && $order->payment_status !== 'paid') {
            $midtrans->syncOrderPaymentStatus($order, $notifications, 'return_status_lookup');
            $order->refresh();
            $paymentSyncError = $midtrans->lastError();
        }

        return view('checkout.payment-success', [
            'order' => $order->load('items.product'),
            'paymentSyncError' => $paymentSyncError,
        ]);
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
            ->with('items.product')
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

    private function authorizeOrderOwner(Order $order): void
    {
        abort_unless($order->user_id === request()->user()?->id, 404);
    }
}
