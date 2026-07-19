<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #17221f; font-size: 11px; }
        h1 { margin: 0 0 4px; font-size: 24px; }
        h2 { margin: 18px 0 8px; font-size: 15px; }
        .muted { color: #64748b; }
        .cards { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .cards td { border: 1px solid #dbe4e2; padding: 10px; width: 25%; }
        .metric { font-size: 17px; font-weight: 800; color: #0f766e; }
        .table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .table th { background: #edf7f4; text-align: left; }
        .table th, .table td { border-bottom: 1px solid #e2e8f0; padding: 8px; vertical-align: top; }
        .bar-wrap { background: #edf7f4; height: 10px; width: 130px; }
        .bar { background: #0f766e; height: 10px; }
    </style>
</head>
<body>
    <h1>Agape153 Sales & Finance Report</h1>
    <div class="muted">{{ $report['start']->format('d M Y') }} - {{ $report['end']->format('d M Y') }}</div>

    <table class="cards">
        <tr>
            <td><div class="muted">Total Revenue</div><div class="metric">Rp{{ number_format($report['totalRevenue'], 0, ',', '.') }}</div></td>
            <td><div class="muted">Paid Revenue</div><div class="metric">Rp{{ number_format($report['paidRevenue'], 0, ',', '.') }}</div></td>
            <td><div class="muted">Unpaid Amount</div><div class="metric">Rp{{ number_format($report['unpaidRevenue'], 0, ',', '.') }}</div></td>
            <td><div class="muted">Average Order</div><div class="metric">Rp{{ number_format($report['averageOrderValue'], 0, ',', '.') }}</div></td>
        </tr>
    </table>

    <h2>Monthly Revenue</h2>
    <table class="table">
        <thead>
            <tr><th>Month</th><th>Orders</th><th>Revenue</th><th>Chart</th></tr>
        </thead>
        <tbody>
            @foreach ($report['monthly'] as $month)
                @php
                    $width = max(3, ((float) $month['revenue'] / $report['maxMonthlyRevenue']) * 130);
                @endphp
                <tr>
                    <td>{{ $month['label'] }}</td>
                    <td>{{ $month['orders'] }}</td>
                    <td>Rp{{ number_format($month['revenue'], 0, ',', '.') }}</td>
                    <td><div class="bar-wrap"><div class="bar" style="width: {{ $width }}px"></div></div></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Top Products</h2>
    <table class="table">
        <thead>
            <tr><th>Product</th><th>Quantity</th><th>Revenue</th></tr>
        </thead>
        <tbody>
            @forelse ($report['topProducts'] as $product)
                <tr>
                    <td>{{ $product['name'] }}</td>
                    <td>{{ number_format($product['quantity']) }}</td>
                    <td>Rp{{ number_format($product['revenue'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No product sales in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
