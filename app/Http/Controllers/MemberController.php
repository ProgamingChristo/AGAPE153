<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReview;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MemberController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('member.dashboard', [
            'orders' => $request->user()->orders()->with('items')->latest()->take(8)->get(),
            'wishlists' => $request->user()->wishlists()->with('product.category')->latest()->take(8)->get(),
        ]);
    }

    public function profile(Request $request)
    {
        return view('member.profile', ['user' => $request->user()]);
    }

    public function purchaseHistory(Request $request)
    {
        return view('member.purchase-history', [
            'orders' => $request->user()
                ->orders()
                ->withCount('items')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function purchaseDetail(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('member.purchase-detail', [
            'order' => $order->load('items.product', 'items.review.repliedBy'),
        ]);
    }

    public function completeOrder(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        if (! $order->canCustomerComplete()) {
            return back()->withErrors(['order' => 'Pesanan belum bisa diselesaikan. Pastikan paket sudah sampai terlebih dahulu.']);
        }

        $order->update([
            'status' => 'completed',
            'shipping_status' => 'completed',
            'customer_completed_at' => now(),
            'delivered_at' => $order->delivered_at ?: now(),
        ]);

        return back()->with('status', 'Pesanan selesai. Terima kasih, kamu sekarang bisa memberi rating produk.');
    }

    public function storeReview(Request $request, OrderItem $item)
    {
        $order = $item->order;

        abort_unless($order?->user_id === $request->user()->id, 404);

        if (! $order->canBeReviewed()) {
            return back()->withErrors(['review' => 'Review dapat diberikan setelah pesanan selesai.']);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1600'],
        ]);

        ProductReview::query()->updateOrCreate(
            ['order_item_id' => $item->id],
            [
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'user_id' => $request->user()->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
                'status' => 'published',
            ]
        );

        return back()->with('status', 'Rating dan komentar produk berhasil disimpan.');
    }

    public function downloadInvoice(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $pdf = new Dompdf($options);
        $pdf->loadHtml(view('member.invoice-pdf', [
            'order' => $order->load('items.product'),
            'siteContact' => view()->shared('siteContact'),
        ])->render());
        $pdf->setPaper('A4');
        $pdf->render();

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$order->order_number.'.pdf"',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:160'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Profil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', 'Password diperbarui.');
    }
}
