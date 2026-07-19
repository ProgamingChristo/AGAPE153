<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.reports.index', [
            'report' => $this->buildReport($request),
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $pdf = new Dompdf($options);
        $pdf->loadHtml(view('admin.reports.pdf', [
            'report' => $this->buildReport($request),
        ])->render());
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="agape153-sales-report.pdf"',
        ]);
    }

    public function downloadCsv(Request $request)
    {
        $report = $this->buildReport($request);

        return response()->streamDownload(function () use ($report): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Agape153 Sales Report', $report['start']->format('d M Y'), $report['end']->format('d M Y')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Order Number', 'Customer', 'Date', 'Status', 'Payment', 'Total']);

            foreach ($report['orders'] as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->customer_name,
                    $order->created_at->format('Y-m-d'),
                    $order->status,
                    $order->payment_status,
                    (float) $order->total_amount,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Top Product', 'Quantity', 'Revenue']);

            foreach ($report['topProducts'] as $product) {
                fputcsv($handle, [$product['name'], $product['quantity'], $product['revenue']]);
            }

            fclose($handle);
        }, 'agape153-sales-report.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function buildReport(Request $request): array
    {
        $start = $this->parseDate($request->input('start_date'), now()->subMonths(5)->startOfMonth())->startOfDay();
        $end = $this->parseDate($request->input('end_date'), now())->endOfDay();

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $orders = Order::query()
            ->with('items')
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $validOrders = $orders->where('status', '!=', 'cancelled');
        $paidOrders = $validOrders->where('payment_status', 'paid');
        $completedOrders = $validOrders->where('status', 'completed');

        $monthly = $this->monthlySeries($validOrders, $start, $end);
        $topProducts = $validOrders
            ->flatMap(fn ($order) => $order->items)
            ->groupBy('product_name')
            ->map(fn ($items, $name) => [
                'name' => $name,
                'quantity' => $items->sum('quantity'),
                'revenue' => $items->sum(fn ($item) => (float) $item->line_total),
            ])
            ->sortByDesc('revenue')
            ->take(8)
            ->values();

        return [
            'start' => $start,
            'end' => $end,
            'orders' => $orders->take(30),
            'monthly' => $monthly,
            'topProducts' => $topProducts,
            'maxMonthlyRevenue' => max($monthly->max('revenue') ?: 1, 1),
            'totalRevenue' => $validOrders->sum(fn ($order) => (float) $order->total_amount),
            'paidRevenue' => $paidOrders->sum(fn ($order) => (float) $order->total_amount),
            'unpaidRevenue' => $validOrders->where('payment_status', 'unpaid')->sum(fn ($order) => (float) $order->total_amount),
            'completedRevenue' => $completedOrders->sum(fn ($order) => (float) $order->total_amount),
            'orderCount' => $validOrders->count(),
            'paidOrderCount' => $paidOrders->count(),
            'cancelledOrderCount' => $orders->where('status', 'cancelled')->count(),
            'averageOrderValue' => $validOrders->count() ? $validOrders->avg(fn ($order) => (float) $order->total_amount) : 0,
        ];
    }

    private function monthlySeries($orders, Carbon $start, Carbon $end)
    {
        $series = collect();
        $cursor = $start->copy()->startOfMonth();
        $finish = $end->copy()->startOfMonth();

        while ($cursor->lessThanOrEqualTo($finish)) {
            $monthOrders = $orders->filter(fn ($order) => $order->created_at->isSameMonth($cursor));

            $series->push([
                'label' => $cursor->format('M Y'),
                'revenue' => $monthOrders->sum(fn ($order) => (float) $order->total_amount),
                'orders' => $monthOrders->count(),
            ]);

            $cursor->addMonth();
        }

        return $series;
    }

    private function parseDate(?string $value, Carbon $fallback): Carbon
    {
        if (! $value) {
            return $fallback->copy();
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return $fallback->copy();
        }
    }
}
