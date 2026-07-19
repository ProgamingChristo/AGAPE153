<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;

class InventoryController extends Controller
{
    public function movements()
    {
        return view('admin.inventory.movements', [
            'movements' => StockMovement::query()->with(['product', 'user'])->latest()->paginate(30),
        ]);
    }
}
