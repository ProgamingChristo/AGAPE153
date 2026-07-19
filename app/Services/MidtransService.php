<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    private ?string $lastError = null;

    public function configure(): void
    {
        Config::$serverKey = trim((string) config('services.midtrans.server_key'));
        Config::$isProduction = filter_var(config('services.midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function isConfigured(): bool
    {
        return filled(trim((string) config('services.midtrans.server_key'))) && filled(trim((string) config('services.midtrans.client_key')));
    }

    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function createSnapTransaction(Order $order): ?array
    {
        $this->lastError = null;

        if (! $this->isConfigured()) {
            $this->lastError = 'Pembayaran online belum dikonfigurasi. Periksa client key dan server key payment gateway di .env.';

            return null;
        }

        $this->configure();

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) round((float) $order->total_amount),
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'shipping_address' => [
                    'address' => $order->shipping_address,
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $this->itemDetails($order),
            'callbacks' => [
                'finish' => route('payments.midtrans.finish'),
                'error' => route('payments.midtrans.finish'),
                'pending' => route('payments.midtrans.finish'),
            ],
        ];

        try {
            $transaction = Snap::createTransaction($payload);

            $order->update([
                'payment_gateway' => 'midtrans',
                'midtrans_order_id' => $order->order_number,
                'midtrans_snap_token' => $transaction->token ?? null,
                'midtrans_redirect_url' => $transaction->redirect_url ?? null,
            ]);

            return [
                'token' => $transaction->token ?? null,
                'redirect_url' => $transaction->redirect_url ?? null,
            ];
        } catch (\Throwable $exception) {
            $this->lastError = $this->friendlyError($exception->getMessage());

            Log::error('Midtrans Snap transaction failed', [
                'order_id' => $order->id,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    public function syncOrderPaymentStatus(Order $order, ?NotificationService $notifications = null, string $source = 'status_lookup'): Order
    {
        $this->lastError = null;

        if (! $this->isConfigured()) {
            $this->lastError = 'Pembayaran online belum dikonfigurasi. Periksa client key dan server key payment gateway di .env.';

            return $order;
        }

        $transactionId = $order->midtrans_order_id ?: $order->order_number;

        if (! $transactionId) {
            $this->lastError = 'Order belum memiliki payment gateway order id.';

            return $order;
        }

        $this->configure();

        try {
            $status = Transaction::status($transactionId);

            return $this->applyPaymentStatus($order, $status, $notifications, $source);
        } catch (\Throwable $exception) {
            $this->lastError = $this->friendlyError($exception->getMessage());

            Log::warning('Midtrans status lookup failed', [
                'order_id' => $order->id,
                'midtrans_order_id' => $transactionId,
                'message' => $exception->getMessage(),
            ]);

            return $order;
        }
    }

    public function applyPaymentStatus(Order $order, array|object $payload, ?NotificationService $notifications = null, string $source = 'notification'): Order
    {
        $data = $this->normalizePayload($payload);
        $transactionStatus = strtolower((string) ($data['transaction_status'] ?? 'unknown'));
        $fraudStatus = strtolower((string) ($data['fraud_status'] ?? ''));
        $previousPaymentStatus = $order->payment_status;

        $updates = [
            'payment_gateway' => 'midtrans',
            'payment_reference' => $data['transaction_id'] ?? $order->payment_reference,
            'midtrans_transaction_id' => $data['transaction_id'] ?? $order->midtrans_transaction_id,
            'payment_payload' => array_merge($order->payment_payload ?? [], [
                $source => $data,
                'last_midtrans_status' => $transactionStatus,
            ]),
        ];

        if (in_array($transactionStatus, ['capture', 'settlement'], true) && (! $fraudStatus || $fraudStatus === 'accept')) {
            $updates['payment_status'] = 'paid';
            $updates['paid_at'] = $order->paid_at ?: now();
        } elseif ($transactionStatus === 'pending') {
            $updates['payment_status'] = 'unpaid';
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true)) {
            $updates['payment_status'] = 'failed';
        } elseif (in_array($transactionStatus, ['refund', 'partial_refund'], true)) {
            $updates['payment_status'] = 'refunded';
        } elseif (($data['status_code'] ?? null) === '200' && $source === 'client_success') {
            $updates['payment_status'] = 'paid';
            $updates['paid_at'] = $order->paid_at ?: now();
        }

        $order->update($updates);
        $order->refresh();

        if ($notifications && $order->payment_status !== $previousPaymentStatus) {
            $message = "Payment status for {$order->order_number} changed to {$order->payment_status}.";
            $notifications->notifyAdmin('payment.updated', 'Payment status updated', $message, ['order_id' => $order->id]);
            $notifications->sendEmail('payment.updated', $order->customer_email, 'Agape153 payment status updated', $message, ['order_id' => $order->id]);
            $notifications->sendWhatsApp('payment.updated', $order->customer_phone, $message, ['order_id' => $order->id]);
        }

        return $order;
    }

    private function itemDetails(Order $order): array
    {
        $items = $order->items->map(fn ($item) => [
            'id' => (string) ($item->product_sku ?: $item->id),
            'price' => (int) round((float) $item->unit_price),
            'quantity' => (int) $item->quantity,
            'name' => str($item->product_name)->limit(45)->toString(),
        ])->values();

        if ((float) $order->shipping_cost > 0) {
            $items->push([
                'id' => 'shipping',
                'price' => (int) round((float) $order->shipping_cost),
                'quantity' => 1,
                'name' => 'Shipping Cost',
            ]);
        }

        if ((float) $order->discount_amount > 0) {
            $items->push([
                'id' => 'discount',
                'price' => -1 * (int) round((float) $order->discount_amount),
                'quantity' => 1,
                'name' => 'Discount',
            ]);
        }

        return $items->all();
    }

    private function friendlyError(string $message): string
    {
        if (str_contains($message, '401') || str_contains(strtolower($message), 'unauthorized')) {
            return 'Payment gateway menolak server key/client key (401 Unauthorized). Periksa lagi client key, server key, huruf besar/kecil, dan mode Sandbox/Production.';
        }

        return "Payment gateway belum bisa membuat token pembayaran: {$message}";
    }

    private function normalizePayload(array|object $payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        return json_decode(json_encode($payload), true) ?: [];
    }

    public function verifySignature(array $payload): bool
    {
        $signature = $payload['signature_key'] ?? null;

        if (! $signature) {
            return false;
        }

        $expected = hash('sha512', ($payload['order_id'] ?? '').($payload['status_code'] ?? '').($payload['gross_amount'] ?? '').config('services.midtrans.server_key'));

        return hash_equals($expected, $signature);
    }
}
