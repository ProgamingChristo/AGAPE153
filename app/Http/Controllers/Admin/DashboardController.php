<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'totalProducts' => Product::query()->count(),
            'totalOrders' => Order::query()->count(),
            'totalMembers' => User::query()->where('role', 'member')->count(),
            'totalViews' => ProductView::query()->count(),
            'recentOrders' => Order::query()->with('items')->latest()->take(6)->get(),
            'topProducts' => Product::query()->orderByDesc('view_count')->take(6)->get(),
        ]);
    }
}
