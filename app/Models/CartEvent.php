<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartEvent extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'event',
        'quantity',
        'source',
        'ip_address',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
