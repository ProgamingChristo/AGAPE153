<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductView extends Model
{
    protected $fillable = ['product_id', 'user_id', 'ip_address', 'country', 'device', 'browser'];
}
