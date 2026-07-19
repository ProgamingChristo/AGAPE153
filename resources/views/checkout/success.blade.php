@extends('layouts.app')

@section('title', 'Order Success - Agape153')

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30" src="https://images.unsplash.com/photo-1528823872057-9c018a7a7553?auto=format&fit=crop&w=1800&q=80" alt="Order created">
            <div class="absolute inset-0 bg-slate-950/72"></div>
        </div>
        <div class="agape-container relative py-12" data-reveal>
            <p class="section-kicker text-amber-200"><x-icon name="check" class="h-4 w-4" />Order Created</p>
            <h1 class="mt-3 break-words text-4xl font-black leading-tight text-white sm:text-6xl">{{ $order->order_number }}</h1>
            <p class="mt-4 max-w-2xl leading-8 text-slate-200">
                @if ($order->payment_method === 'midtrans')
                    Order berhasil dibuat. Popup pembayaran online akan muncul otomatis.
                @else
                    Order berhasil dibuat. Anda akan diarahkan ke WhatsApp untuk konfirmasi order.
                @endif
            </p>
        </div>
    </section>

    <section class="section-pad bg-[#f8faf9]">
        <div class="agape-container max-w-4xl">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8" data-reveal>
                <dl class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl bg-[#f8faf9] p-4">
                        <dt class="text-sm text-slate-500">Tracking Code</dt>
                        <dd class="mt-1 font-black text-slate-950">{{ $order->tracking_code }}</dd>
                    </div>
                    <div class="rounded-2xl bg-[#f8faf9] p-4">
                        <dt class="text-sm text-slate-500">Total</dt>
                        <dd class="mt-1 font-black text-teal-800">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="rounded-2xl bg-[#f8faf9] p-4">
                        <dt class="text-sm text-slate-500">Payment</dt>
                        <dd class="mt-1 font-black text-slate-950">{{ $order->payment_method === 'midtrans' ? 'ONLINE' : strtoupper($order->payment_method) }} / {{ strtoupper($order->payment_status) }}</dd>
                    </div>
                </dl>

                <div class="mt-8 flex flex-wrap gap-3">
                    @if ($order->payment_status === 'paid')
                        <a class="btn-primary" href="{{ route('checkout.payment-success', $order) }}">
                            <x-icon name="check" class="h-4 w-4" />
                            Lihat Invoice Berhasil
                        </a>
                    @endif
                    @if ($order->payment_method === 'midtrans' && $order->payment_status !== 'paid')
                        @if ($order->midtrans_snap_token)
                            <button class="btn-primary" id="pay-online" type="button">
                                <x-icon name="lock" class="h-4 w-4" />
                                Bayar Online
                            </button>
                            <a class="btn-secondary" href="{{ $order->midtrans_redirect_url }}" target="_blank" rel="noopener">Open Payment Page</a>
                        @else
                            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-800">
                                {{ $midtransError ?: 'Token pembayaran online belum tersedia. Periksa konfigurasi payment gateway di .env.' }}
                            </div>
                        @endif
                    @endif
                    @if ($order->payment_method === 'whatsapp' && $order->wa_checkout_url)
                        <a class="btn-secondary" href="{{ $order->wa_checkout_url }}" target="_blank" rel="noopener">
                            <x-icon name="phone" class="h-4 w-4" />
                            Confirm via WhatsApp
                        </a>
                    @endif
                    <a class="btn-secondary" href="{{ route('orders.track') }}">
                        <x-icon name="search" class="h-4 w-4" />
                        Track Order
                    </a>
                </div>
            </div>
        </div>
    </section>

    @if ($order->payment_method === 'midtrans' && $order->midtrans_snap_token && $midtransClientKey)
        <script src="{{ $midtransIsProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $midtransClientKey }}"></script>
        <script>
            const openOnlinePayment = () => {
                if (!window.snap) {
                    return;
                }

                window.snap.pay(@json($order->midtrans_snap_token), {
                    onSuccess: function (result) {
                        fetch(@json(route('checkout.midtrans-client-success', $order)), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(result || {})
                        })
                            .then(response => response.json())
                            .then(data => {
                                window.location.href = data.redirect_url || @json(route('checkout.payment-success', $order));
                            })
                            .catch(() => {
                                window.location.href = @json(route('checkout.payment-success', $order));
                            });
                    },
                    onPending: function () {
                        window.location.href = @json(route('checkout.success', $order));
                    },
                    onError: function () {
                        alert('Pembayaran belum berhasil. Silakan coba lagi atau gunakan WhatsApp.');
                    }
                });
            };

            document.getElementById('pay-online')?.addEventListener('click', openOnlinePayment);

            @if ($autoOpenMidtrans)
                window.addEventListener('load', () => setTimeout(openOnlinePayment, 450));
            @endif
        </script>
    @endif
@endsection
