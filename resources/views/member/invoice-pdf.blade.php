<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #17221f; font-size: 12px; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 16px; margin-bottom: 20px; }
        .logo { width: 96px; margin-bottom: 10px; }
        .muted { color: #64748b; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid th { text-align: left; background: #edf7f4; padding: 10px; }
        .grid td { border-bottom: 1px solid #e2e8f0; padding: 10px; vertical-align: top; }
        .summary { margin-top: 20px; width: 280px; margin-left: auto; }
        .summary td { padding: 6px 0; }
        .total { font-size: 16px; font-weight: 800; color: #0f766e; }
    </style>
</head>
<body>
    <div class="header">
        @php
            $logoPath = public_path('images/agape153-logo.png');
        @endphp
        @if (file_exists($logoPath))
            <img class="logo" src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Agape153">
        @endif
        <div class="muted">Indonesian Agriculture International Commodity Trading</div>
        <div class="muted">{{ $siteContact['email'] ?? 'info@agape153.com' }} / {{ $siteContact['phone'] ?? '+62816795153' }}</div>
    </div>

    <h1>Invoice / Order Summary</h1>
    <p><strong>Order:</strong> {{ $order->order_number }}</p>
    <p><strong>Date:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
    <p><strong>Customer:</strong> {{ $order->customer_name }} {{ $order->company_name ? '/ '.$order->company_name : '' }}</p>
    <p><strong>Contact:</strong> {{ $order->customer_email ?: '-' }} / {{ $order->customer_phone }}</p>
    <p><strong>Shipping:</strong> {{ $order->shipping_address }}, {{ $order->country }}</p>

    <table class="grid">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name }}</strong><br>
                        <span class="muted">{{ $item->product_sku ?: '-' }}</span>
                    </td>
                    <td>{{ $item->quantity }} {{ $item->unit }}</td>
                    <td>Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item->line_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr><td>Subtotal</td><td style="text-align:right">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Shipping</td><td style="text-align:right">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</td></tr>
        <tr><td class="total">Total</td><td class="total" style="text-align:right">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td></tr>
    </table>

    <p class="muted" style="margin-top: 28px;">Payment status: {{ strtoupper($order->payment_status) }} / Order status: {{ $order->statusLabel() }} / Shipping: {{ $order->tracking_code ?: '-' }}</p>
</body>
</html>
