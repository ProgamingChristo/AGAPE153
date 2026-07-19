<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\StockMovement;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->input('status')))
            ->when($request->filled('payment_status'), fn ($query) => $query->where('payment_status', (string) $request->input('payment_status')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = (string) $request->input('q');
                $query->where(function ($inner) use ($term): void {
                    $inner->where('order_number', 'like', "%{$term}%")
                        ->orWhere('customer_name', 'like', "%{$term}%")
                        ->orWhere('customer_phone', 'like', "%{$term}%")
                        ->orWhere('tracking_code', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => ['pending', 'confirmed', 'processing', 'shipped', 'completed', 'cancelled'],
            'paymentStatuses' => ['unpaid', 'paid', 'failed', 'refunded'],
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', [
            'order' => $order->load(['items.product', 'items.review.user', 'items.review.repliedBy', 'user', 'acceptedBy']),
            'statuses' => ['pending', 'confirmed', 'processing', 'shipped', 'completed', 'cancelled'],
            'paymentStatuses' => ['unpaid', 'paid', 'failed', 'refunded'],
            'shippingStatuses' => Order::shippingStatusOptions(),
        ]);
    }

    public function accept(Request $request, Order $order, NotificationService $notifications)
    {
        if (! $order->canBeAccepted()) {
            return back()->with('status', 'Order ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($request, $order): void {
            $order->update([
                'status' => 'confirmed',
                'accepted_at' => now(),
                'accepted_by' => $request->user()->id,
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $request->user()->id,
                'shipping_status' => 'confirmed',
            ]);

            $this->reduceStockForOrder($order->load('items.product'), $request);
        });

        $message = "Order {$order->order_number} has been accepted by Agape153.";
        $notifications->notifyAdmin('order.accepted', 'Order accepted', $message, ['order_id' => $order->id]);
        $notifications->sendEmail('order.accepted', $order->customer_email, 'Agape153 order accepted', $message, ['order_id' => $order->id]);
        $notifications->sendWhatsApp('order.accepted', $order->customer_phone, $message, ['order_id' => $order->id]);

        return back()->with('status', 'Order berhasil di-ACC dan masuk status confirmed.');
    }

    public function update(Request $request, Order $order, NotificationService $notifications)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipped,completed,cancelled'],
            'payment_status' => ['required', 'in:unpaid,paid,failed,refunded'],
            'tracking_code' => ['nullable', 'string', 'max:80'],
            'shipping_provider' => ['nullable', 'string', 'max:80'],
            'shipping_status' => ['nullable', 'in:'.implode(',', array_keys(Order::shippingStatusOptions()))],
            'tracking_url' => ['nullable', 'url', 'max:500'],
            'shipping_notes' => ['nullable', 'string', 'max:2000'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'quotation_status' => ['required', 'in:draft,sent,accepted,rejected'],
            'quotation_notes' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['shipping_cost'] = (float) ($data['shipping_cost'] ?? 0);
        $data['discount_amount'] = (float) ($data['discount_amount'] ?? 0);
        $data['total_amount'] = max(0, (float) $order->subtotal - $data['discount_amount'] + $data['shipping_cost']);
        if ($request->has('tracking_url') || $request->has('shipping_provider') || $request->has('tracking_code')) {
            $data['tracking_url'] = ($data['tracking_url'] ?? null) ?: $this->trackingUrl($data['shipping_provider'] ?? null, $data['tracking_code'] ?? null);
        }
        if (array_key_exists('shipping_status', $data) && $data['shipping_status']) {
            $data = $this->applyShipmentState($order, $data);
        }
        $previousPaymentStatus = $order->payment_status;
        $previousStatus = $order->status;

        if ($order->status === 'pending' && $data['status'] === 'confirmed') {
            $data['accepted_at'] = now();
            $data['accepted_by'] = $request->user()->id;
            $data['approval_status'] = 'approved';
            $data['approved_at'] = now();
            $data['approved_by'] = $request->user()->id;
            $data['shipping_status'] = $data['shipping_status'] ?? 'confirmed';
        }

        if ($data['discount_amount'] > 0 && $order->status === 'pending' && $data['status'] === 'pending') {
            $data['approval_status'] = 'needs_review';
        }

        DB::transaction(function () use ($request, $order, $data): void {
            $shouldReduceStock = $order->status === 'pending' && $data['status'] === 'confirmed';

            $order->update($data);

            if ($shouldReduceStock) {
                $this->reduceStockForOrder($order->load('items.product'), $request);
            }
        });

        if ($previousPaymentStatus !== $order->payment_status) {
            $message = "Payment status for {$order->order_number} changed to {$order->payment_status}.";
            $notifications->sendEmail('payment.updated', $order->customer_email, 'Agape153 payment status updated', $message, ['order_id' => $order->id]);
            $notifications->sendWhatsApp('payment.updated', $order->customer_phone, $message, ['order_id' => $order->id]);
        }

        if ($previousStatus !== $order->status) {
            $message = "Order {$order->order_number} status changed to {$order->statusLabel()}.";
            $notifications->sendEmail('order.status_updated', $order->customer_email, 'Agape153 order status updated', $message, ['order_id' => $order->id]);
            $notifications->sendWhatsApp('order.status_updated', $order->customer_phone, $message, ['order_id' => $order->id]);
        }

        return back()->with('status', 'Order berhasil diperbarui.');
    }

    public function updateShipment(Request $request, Order $order, NotificationService $notifications)
    {
        $data = $request->validate([
            'tracking_code' => ['nullable', 'string', 'max:80'],
            'shipping_provider' => ['nullable', 'string', 'max:80'],
            'shipping_status' => ['required', 'in:'.implode(',', array_keys(Order::shippingStatusOptions()))],
            'tracking_url' => ['nullable', 'url', 'max:500'],
            'shipping_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($order->status === 'pending' && $data['shipping_status'] !== 'order_created') {
            return back()->withErrors(['shipping_status' => 'ACC order terlebih dahulu sebelum memproses resi pengiriman.']);
        }

        $data['tracking_url'] = ($data['tracking_url'] ?? null) ?: $this->trackingUrl($data['shipping_provider'] ?? null, $data['tracking_code'] ?? null);
        $data = $this->applyShipmentState($order, $data);
        $previousStatus = $order->status;
        $previousShippingStatus = $order->shipping_status;

        $order->update($data);

        if ($previousStatus !== $order->status || $previousShippingStatus !== $order->shipping_status) {
            $message = "Shipping update for {$order->order_number}: {$order->shippingStatusLabel()}.";
            $notifications->sendEmail('shipment.updated', $order->customer_email, 'Agape153 shipment update', $message, ['order_id' => $order->id]);
            $notifications->sendWhatsApp('shipment.updated', $order->customer_phone, $message, ['order_id' => $order->id]);
        }

        return back()->with('status', 'Resi dan status pengiriman berhasil diperbarui.');
    }

    public function shippingLabel(Order $order)
    {
        return view('admin.orders.shipping-label', [
            'order' => $order->load('items.product'),
            'siteContact' => view()->shared('siteContact'),
        ]);
    }

    public function replyReview(Request $request, ProductReview $review, NotificationService $notifications)
    {
        $data = $request->validate([
            'admin_reply' => ['required', 'string', 'max:2000'],
        ]);

        $review->update([
            'admin_reply' => $data['admin_reply'],
            'replied_by' => $request->user()->id,
            'replied_at' => now(),
        ]);

        if ($review->user?->email) {
            $notifications->sendEmail(
                'review.replied',
                $review->user->email,
                'Agape153 replied to your product review',
                "Agape153 replied to your review for {$review->product?->name}: {$data['admin_reply']}",
                ['review_id' => $review->id, 'product_id' => $review->product_id]
            );
        }

        return back()->with('status', 'Balasan review berhasil disimpan.');
    }

    private function reduceStockForOrder(Order $order, Request $request): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if (! $product) {
                continue;
            }

            $before = (int) $product->stock_quantity;
            $after = max(0, $before - (int) $item->quantity);

            $product->update(['stock_quantity' => $after]);

            StockMovement::query()->create([
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
                'quantity_before' => $before,
                'quantity_after' => $after,
                'delta' => $after - $before,
                'type' => 'order_acceptance',
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'reason' => "Order {$order->order_number} accepted",
            ]);

            if ($after <= 5) {
                app(NotificationService::class)->notifyAdmin(
                    'inventory.low_stock',
                    'Low stock alert',
                    "Low stock alert: {$product->name} now has {$after} {$product->unit} after accepting {$order->order_number}.",
                    ['product_id' => $product->id, 'order_id' => $order->id, 'stock' => $after]
                );
            }
        }
    }

    private function applyShipmentState(Order $order, array $data): array
    {
        $shippingStatus = $data['shipping_status'] ?? $order->shipping_status;

        if (! $shippingStatus) {
            return $data;
        }

        if (in_array($shippingStatus, ['packed'], true) && in_array($order->status, ['pending', 'confirmed'], true)) {
            $data['status'] = 'processing';
        }

        if (in_array($shippingStatus, ['handover', 'in_transit', 'out_for_delivery', 'delivered'], true)) {
            $data['status'] = 'shipped';
            $data['shipped_at'] = $order->shipped_at ?: now();
        }

        if ($shippingStatus === 'delivered') {
            $data['delivered_at'] = $order->delivered_at ?: now();
        }

        if ($shippingStatus === 'completed') {
            $data['status'] = 'completed';
            $data['delivered_at'] = $order->delivered_at ?: now();
            $data['customer_completed_at'] = $order->customer_completed_at ?: now();
        }

        return $data;
    }

    private function trackingUrl(?string $provider, ?string $trackingCode): ?string
    {
        if (! $provider || ! $trackingCode) {
            return null;
        }

        $provider = str($provider)->lower()->toString();
        $code = rawurlencode($trackingCode);

        return match (true) {
            str_contains($provider, 'dhl') => "https://www.dhl.com/id-en/home/tracking/tracking-express.html?submit=1&tracking-id={$code}",
            str_contains($provider, 'fedex') => "https://www.fedex.com/fedextrack/?trknbr={$code}",
            str_contains($provider, 'ups') => "https://www.ups.com/track?tracknum={$code}",
            str_contains($provider, 'jne') => 'https://www.jne.co.id/id/tracking/trace',
            default => null,
        };
    }
}
