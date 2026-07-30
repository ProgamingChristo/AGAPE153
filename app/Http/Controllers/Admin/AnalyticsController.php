<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CartEvent;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductView;
use App\Services\WebsiteTrafficService;

class AnalyticsController extends Controller
{
    public function index(WebsiteTrafficService $traffic)
    {
        $productViews = ProductView::query()->count();
        $cartSessions = CartEvent::query()->where('event', 'added')->distinct('session_id')->count('session_id');
        $orderCount = Order::query()->count();
        $paidOrders = Order::query()->where('payment_status', 'paid')->count();
        $abandonedCartEstimate = max($cartSessions - $orderCount, 0);

        return view('admin.analytics.index', [
            'websiteTraffic' => $traffic->publicStats(),
            'trafficTrend' => $traffic->dailyTrend(),
            'topPages' => $traffic->topPages(),
            'funnel' => [
                ['label' => 'Product Views', 'value' => $productViews],
                ['label' => 'Cart Sessions', 'value' => $cartSessions],
                ['label' => 'Orders Created', 'value' => $orderCount],
                ['label' => 'Paid Orders', 'value' => $paidOrders],
            ],
            'abandonedCartEstimate' => $abandonedCartEstimate,
            'productDemand' => OrderItem::query()
                ->selectRaw('product_name, SUM(quantity) as quantity, SUM(line_total) as revenue')
                ->groupBy('product_name')
                ->orderByDesc('quantity')
                ->take(10)
                ->get(),
            'deviceTraffic' => $traffic->deviceTraffic(),
            'sourceTraffic' => $traffic->sourceTraffic(),
        ]);
    }
}
