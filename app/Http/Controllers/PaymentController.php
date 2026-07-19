<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function midtransFinish(Request $request, MidtransService $midtrans, NotificationService $notifications)
    {
        $orderId = $request->query('order_id');

        $order = Order::query()
            ->where('midtrans_order_id', $orderId)
            ->orWhere('order_number', $orderId)
            ->first();

        if ($order) {
            $midtrans->syncOrderPaymentStatus($order, $notifications, 'finish_status_lookup');

            if ($request->user() && $order->user_id === $request->user()->id) {
                return redirect()->route($order->payment_status === 'paid' ? 'checkout.payment-success' : 'checkout.success', $order);
            }
        }

        return redirect()
            ->route('orders.track')
            ->with('status', 'Pembayaran diproses. Masukkan nomor order untuk melihat status terbaru.');
    }

    public function midtransNotification(Request $request, MidtransService $midtrans, NotificationService $notifications)
    {
        $payload = $request->all();

        if (! $midtrans->verifySignature($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::query()
            ->where('midtrans_order_id', $payload['order_id'] ?? null)
            ->orWhere('order_number', $payload['order_id'] ?? null)
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $midtrans->applyPaymentStatus($order, $payload, $notifications, 'notification');

        return response()->json(['message' => 'OK']);
    }
}
