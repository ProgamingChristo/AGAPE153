<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'company_name',
        'country',
        'shipping_address',
        'status',
        'accepted_at',
        'accepted_by',
        'approval_status',
        'approved_at',
        'approved_by',
        'payment_method',
        'payment_gateway',
        'payment_reference',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_snap_token',
        'midtrans_redirect_url',
        'payment_status',
        'paid_at',
        'payment_payload',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'total_amount',
        'tracking_code',
        'shipping_provider',
        'shipping_status',
        'tracking_url',
        'shipped_at',
        'delivered_at',
        'customer_completed_at',
        'shipping_events',
        'shipping_notes',
        'quotation_status',
        'quotation_notes',
        'wa_checkout_url',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'accepted_at' => 'datetime',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
            'payment_payload' => 'array',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'customer_completed_at' => 'datetime',
            'shipping_events' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function canBeAccepted(): bool
    {
        return $this->status === 'pending';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Menunggu',
        };
    }

    public static function shippingStatusOptions(): array
    {
        return [
            'order_created' => 'Order dibuat',
            'confirmed' => 'Order dikonfirmasi',
            'packed' => 'Pesanan dikemas',
            'handover' => 'Diserahkan ke kurir',
            'in_transit' => 'Dalam perjalanan',
            'out_for_delivery' => 'Menuju alamat customer',
            'delivered' => 'Sampai di tujuan',
            'completed' => 'Pesanan selesai',
        ];
    }

    public function shippingStatusLabel(): string
    {
        return self::shippingStatusOptions()[$this->shipping_status ?: 'order_created'] ?? str($this->shipping_status)->headline()->toString();
    }

    public function canCustomerComplete(): bool
    {
        return $this->user_id !== null
            && $this->status === 'shipped'
            && $this->shipping_status === 'delivered';
    }

    public function canBeReviewed(): bool
    {
        return $this->status === 'completed';
    }

    public function shipmentTimeline(): array
    {
        $current = $this->shipping_status ?: match ($this->status) {
            'confirmed' => 'confirmed',
            'processing' => 'packed',
            'shipped' => 'in_transit',
            'completed' => 'completed',
            default => 'order_created',
        };

        $steps = [
            'order_created' => [
                'title' => 'Order dibuat',
                'description' => 'Pesanan masuk ke sistem Agape153.',
                'time' => $this->created_at,
            ],
            'confirmed' => [
                'title' => 'Order dikonfirmasi',
                'description' => 'Admin sudah ACC order dan menyiapkan stok.',
                'time' => $this->accepted_at,
            ],
            'packed' => [
                'title' => 'Pesanan dikemas',
                'description' => 'Produk sedang disiapkan untuk pengiriman.',
                'time' => $this->shipped_at,
            ],
            'handover' => [
                'title' => 'Diserahkan ke kurir',
                'description' => trim(($this->shipping_provider ?: 'Kurir').' menerima paket untuk proses kirim.'),
                'time' => $this->shipped_at,
            ],
            'in_transit' => [
                'title' => 'Dalam perjalanan',
                'description' => 'Paket sedang bergerak menuju kota/alamat tujuan.',
                'time' => $this->shipped_at,
            ],
            'out_for_delivery' => [
                'title' => 'Menuju alamat customer',
                'description' => 'Kurir sedang mengantar paket ke alamat tujuan.',
                'time' => $this->delivered_at,
            ],
            'delivered' => [
                'title' => 'Sampai di tujuan',
                'description' => 'Paket ditandai sudah sampai, customer dapat menyelesaikan pesanan.',
                'time' => $this->delivered_at,
            ],
            'completed' => [
                'title' => 'Pesanan selesai',
                'description' => 'Customer menyelesaikan pesanan dan dapat memberi rating produk.',
                'time' => $this->customer_completed_at,
            ],
        ];

        $keys = array_keys($steps);
        $currentIndex = array_search($current, $keys, true);
        $currentIndex = $currentIndex === false ? 0 : $currentIndex;

        if ($this->status === 'completed') {
            $currentIndex = array_search('completed', $keys, true);
        }

        return collect($steps)
            ->map(function (array $step, string $key) use ($keys, $currentIndex): array {
                $index = array_search($key, $keys, true);

                return [
                    ...$step,
                    'key' => $key,
                    'is_done' => $index <= $currentIndex,
                    'is_current' => $index === $currentIndex,
                ];
            })
            ->values()
            ->all();
    }
}
