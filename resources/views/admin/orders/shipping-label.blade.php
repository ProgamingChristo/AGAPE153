<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resi {{ $order->order_number }} - Agape153</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef3f0; color: #101820; font-family: Arial, sans-serif; }
        .sheet { width: min(760px, calc(100% - 24px)); margin: 24px auto; background: #fff; border: 1px solid #d7e0dd; }
        .stripe { display: grid; grid-template-columns: repeat(3, 1fr); height: 8px; }
        .stripe span:nth-child(1) { background: #e64b3c; }
        .stripe span:nth-child(2) { background: #e9c95a; }
        .stripe span:nth-child(3) { background: #2d9db7; }
        .wrap { padding: 24px; }
        .header { display: flex; justify-content: space-between; gap: 24px; border-bottom: 2px solid #101820; padding-bottom: 18px; }
        .logo { width: 112px; height: auto; display: block; margin-bottom: 10px; }
        .title { text-align: right; }
        .title h1 { margin: 0; font-size: 26px; }
        .title div { margin-top: 6px; font-weight: 700; color: #475569; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-top: 20px; }
        .box { border: 1px solid #d7e0dd; padding: 16px; min-height: 150px; }
        .label { font-size: 11px; font-weight: 900; letter-spacing: .14em; text-transform: uppercase; color: #64748b; }
        .value { margin-top: 8px; font-size: 16px; font-weight: 800; line-height: 1.5; }
        .tracking { margin-top: 20px; border: 2px dashed #101820; padding: 18px; text-align: center; }
        .tracking .code { margin-top: 8px; font-size: 34px; font-weight: 900; letter-spacing: .08em; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 12px; text-align: left; font-size: 13px; }
        th { background: #f8faf9; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; color: #475569; }
        .actions { width: min(760px, calc(100% - 24px)); margin: 0 auto 24px; display: flex; justify-content: flex-end; gap: 8px; }
        button { border: 0; border-radius: 999px; background: #101820; color: white; padding: 12px 18px; font-weight: 800; cursor: pointer; }
        @media print {
            body { background: #fff; }
            .sheet { width: 100%; margin: 0; border: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="stripe"><span></span><span></span><span></span></div>
        <div class="wrap">
            <div class="header">
                <div>
                    <img class="logo" src="{{ asset('images/agape153-logo.png') }}" alt="Agape153">
                    <div class="value">{{ $siteContact['email'] ?? 'info.agape153@gmail.com' }}</div>
                    <div>{{ $siteContact['phone'] ?? '+62816795153' }}</div>
                </div>
                <div class="title">
                    <h1>Shipping Label</h1>
                    <div>{{ $order->order_number }}</div>
                    <div>{{ now()->format('d M Y H:i') }}</div>
                </div>
            </div>

            <div class="grid">
                <div class="box">
                    <div class="label">Sender</div>
                    <div class="value">Agape153</div>
                    <div>Jakarta, Indonesia</div>
                    <div>{{ $siteContact['phone'] ?? '+62816795153' }}</div>
                </div>
                <div class="box">
                    <div class="label">Receiver</div>
                    <div class="value">{{ $order->customer_name }}</div>
                    <div>{{ $order->customer_phone }}</div>
                    <div>{{ $order->shipping_address }}, {{ $order->country }}</div>
                </div>
            </div>

            <div class="tracking">
                <div class="label">Courier / Receipt Number</div>
                <div class="code">{{ $order->tracking_code ?: $order->order_number }}</div>
                <div class="value">{{ $order->shipping_provider ?: 'Agape153 Internal Delivery' }} - {{ $order->shippingStatusLabel() }}</div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>SKU</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }} {{ $item->unit }}</td>
                            <td>{{ $item->product_sku ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($order->shipping_notes)
                <div class="box" style="margin-top: 20px; min-height: auto;">
                    <div class="label">Shipping Notes</div>
                    <div class="value">{{ $order->shipping_notes }}</div>
                </div>
            @endif
        </div>
    </div>
    <div class="actions">
        <button onclick="window.print()">Print Resi</button>
    </div>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
