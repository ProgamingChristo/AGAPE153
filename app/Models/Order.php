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
        'payment_method',
        'payment_status',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'tracking_code',
        'wa_checkout_url',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total_amount' => 'decimal:2',
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
}
