<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Models\WhatsAppVerificationCode;
use App\Support\MediaUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_about_page(): void
    {
        $this->get('/')
            ->assertRedirect(route('about'));
    }

    public function test_about_page_returns_successfully(): void
    {
        $this->get(route('about'))
            ->assertStatus(200)
            ->assertSee('Indonesian Agriculture');
    }

    public function test_catalog_and_product_detail_return_successfully(): void
    {
        $category = Category::query()->create([
            'name' => 'Rempah-rempah',
            'slug' => 'rempah-rempah',
            'type' => 'spice',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Lada Hitam',
            'slug' => 'lada-hitam',
            'short_description' => 'Lada hitam Indonesia.',
            'product_details' => [
                ['label' => 'Product Name', 'value' => 'Black Pepper'],
                ['label' => 'Origin', 'value' => 'Indonesia'],
                ['label' => 'Quality', 'value' => 'Export Grade'],
                ['label' => 'Form', 'value' => 'Whole Dried Peppercorns'],
                ['label' => 'Color', 'value' => 'Black'],
                ['label' => 'Moisture', 'value' => 'Max. 13%'],
                ['label' => 'Purity', 'value' => 'Min. 99%'],
                ['label' => 'Packaging', 'value' => '25 kg or 50 kg PP Bags (Custom packaging available)'],
            ],
            'unit' => 'Kg',
            'price' => 98000,
            'currency' => 'IDR',
            'min_order_quantity' => 100,
            'stock_quantity' => 20000,
            'is_active' => true,
            'image_url' => 'https://example.com/lada.jpg',
            'video_url' => '/videos/catalog/lada-hitam.html',
        ]);

        $this->get('/products')->assertStatus(200)->assertSee('Lada Hitam');
        $this->get(route('products.show', $product))
            ->assertStatus(200)
            ->assertSee('Lada Hitam')
            ->assertSee('Product Details')
            ->assertSee('Product Name')
            ->assertSee('Export Grade')
            ->assertSee('Whole Dried Peppercorns')
            ->assertSee('Min. 99%')
            ->assertSee('Max. 13%')
            ->assertSee('25 kg or 50 kg PP Bags')
            ->assertSee('Please contact or drop us email')
            ->assertDontSee('Rp98.000/Kg')
            ->assertSee('100 kgs')
            ->assertSee('20,000 kgs')
            ->assertSee('Product Video');
    }

    public function test_cart_can_receive_product(): void
    {
        $category = Category::query()->create([
            'name' => 'Robusta Coffee',
            'slug' => 'robusta-coffee',
            'type' => 'coffee',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Robusta Grade A Jambi',
            'slug' => 'robusta-grade-a-jambi',
            'unit' => 'Kg',
            'price' => 80000,
            'currency' => 'IDR',
            'stock_quantity' => 10,
            'is_active' => true,
            'image_url' => 'https://example.com/robusta.jpg',
        ]);

        $this->post(route('cart.store', $product), ['quantity' => 2])
            ->assertRedirect(route('login'));

        $member = User::query()->create([
            'name' => 'Cart Buyer',
            'email' => 'cart-buyer@example.com',
            'password' => 'password',
            'role' => 'member',
            'status' => 'active',
        ]);

        $this->actingAs($member)
            ->post(route('cart.store', $product), ['quantity' => 2])
            ->assertSessionHas('cart');

        $this->actingAs($member)
            ->get(route('cart.index'))
            ->assertStatus(200)
            ->assertSee('Robusta Grade A Jambi');
    }

    public function test_admin_area_requires_authentication(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_sets_admin_session_token(): void
    {
        User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('admin_session_token');

        $this->get(route('admin.dashboard'))->assertStatus(200);
    }

    public function test_member_can_reset_password_with_email_link(): void
    {
        Notification::fake();

        $user = User::query()->create([
            'name' => 'Reset Buyer',
            'email' => 'reset-buyer@example.com',
            'password' => 'old-password',
            'role' => 'member',
            'status' => 'active',
        ]);

        $this->get(route('password.request'))
            ->assertStatus(200)
            ->assertSee('Forgot password');

        $this->post(route('password.email'), [
            'email' => 'reset-buyer@example.com',
        ])->assertSessionHas('status');

        $token = PasswordBroker::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'reset-buyer@example.com',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-password-123', $user->refresh()->password));
    }

    public function test_member_can_register_and_login_with_whatsapp_number(): void
    {
        $this->post(route('register.whatsapp'), [
            'name' => 'WhatsApp Buyer',
            'phone' => '081234567890',
            'company_name' => 'WA Trading',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
        ])->assertRedirect(route('register.whatsapp.verify.form', ['phone' => '6281234567890']));

        $verification = WhatsAppVerificationCode::query()
            ->where('phone', '6281234567890')
            ->where('purpose', 'register')
            ->firstOrFail();

        $this->post(route('register.whatsapp.verify'), [
            'phone' => '081234567890',
            'code' => $verification->code,
        ])->assertRedirect(route('member.dashboard'));

        $user = User::query()->where('phone', '6281234567890')->firstOrFail();

        $this->assertSame('whatsapp', $user->auth_provider);
        $this->assertStringEndsWith('@agape153.local', $user->email);
        $this->assertNotNull($user->phone_verified_at);

        $this->post(route('logout'));

        $this->post(route('login.store'), [
            'login' => '081234567890',
            'password' => 'password-123',
        ])->assertRedirect(route('member.dashboard'));
    }

    public function test_google_auth_redirect_requires_configuration(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $this->from(route('login'))
            ->get(route('auth.google.redirect'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');
    }

    public function test_member_can_view_purchase_history(): void
    {
        $member = User::query()->create([
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'password' => 'password',
            'role' => 'member',
            'status' => 'active',
        ]);

        $order = Order::query()->create([
            'order_number' => 'AGP-TEST-001',
            'user_id' => $member->id,
            'customer_name' => 'Buyer',
            'customer_email' => 'buyer@example.com',
            'customer_phone' => '628111222333',
            'country' => 'Indonesia',
            'shipping_address' => 'Jakarta',
            'subtotal' => 80000,
            'total_amount' => 80000,
            'tracking_code' => 'TRKTEST001',
        ]);

        $this->actingAs($member)
            ->get(route('member.purchase-history'))
            ->assertStatus(200)
            ->assertSee('AGP-TEST-001');

        $this->actingAs($member)
            ->get(route('member.purchase-detail', $order))
            ->assertStatus(200)
            ->assertSee('TRKTEST001');
    }

    public function test_contact_form_creates_admin_message(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Global Buyer',
            'email' => 'buyer@example.com',
            'phone' => '+62816795153',
            'company_name' => 'Buyer Trading',
            'interest' => 'Spices',
            'message' => 'We need black pepper for export inquiry.',
        ])->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'buyer@example.com',
            'status' => 'new',
        ]);
    }

    public function test_admin_can_reply_contact_message(): void
    {
        Mail::fake();

        $admin = User::query()->create([
            'name' => 'Message Admin',
            'email' => 'message-admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $message = ContactMessage::query()->create([
            'name' => 'Global Buyer',
            'email' => 'buyer@example.com',
            'phone' => '+62816795153',
            'company_name' => 'Buyer Trading',
            'interest' => 'Spices',
            'message' => 'We need black pepper for export inquiry.',
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->withSession(['admin_session_token' => 'test-token', 'admin_last_activity' => time()])
            ->post(route('admin.contact-messages.reply', $message), [
                'reply_subject' => 'Agape153 export inquiry reply',
                'reply_message' => 'Thank you, our sales team will prepare the quotation.',
            ])
            ->assertSessionHas('status');

        $message->refresh();

        $this->assertSame('replied', $message->status);
        $this->assertSame('Agape153 export inquiry reply', $message->reply_subject);
        $this->assertNotNull($message->replied_at);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'event' => 'contact.replied',
            'recipient' => 'buyer@example.com',
        ]);
    }

    public function test_member_can_download_purchase_invoice_pdf(): void
    {
        $member = User::query()->create([
            'name' => 'Invoice Buyer',
            'email' => 'invoice@example.com',
            'password' => 'password',
            'role' => 'member',
            'status' => 'active',
        ]);

        $order = Order::query()->create([
            'order_number' => 'AGP-PDF-001',
            'user_id' => $member->id,
            'customer_name' => 'Invoice Buyer',
            'customer_email' => 'invoice@example.com',
            'customer_phone' => '+62816795153',
            'country' => 'Indonesia',
            'shipping_address' => 'Jakarta',
            'subtotal' => 80000,
            'total_amount' => 80000,
            'tracking_code' => 'TRKPDF001',
        ]);

        $order->items()->create([
            'product_name' => 'Robusta Grade A Jambi',
            'quantity' => 1,
            'unit' => 'Kg',
            'unit_price' => 80000,
            'line_total' => 80000,
        ]);

        $this->actingAs($member)
            ->get(route('member.purchase-invoice', $order))
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_upload_product_image_file(): void
    {
        Storage::fake('public');

        $admin = User::query()->create([
            'name' => 'Admin Upload',
            'email' => 'upload-admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $category = Category::query()->create([
            'name' => 'Coffee',
            'slug' => 'coffee',
            'type' => 'coffee',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->withSession(['admin_session_token' => 'test-token', 'admin_last_activity' => time()])
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Arabica Upload Test',
                'slug' => 'arabica-upload-test',
                'unit' => 'Kg',
                'price' => 120000,
                'currency' => 'IDR',
                'min_order_quantity' => 1,
                'stock_quantity' => 5,
                'is_active' => 1,
                'detail_labels' => ['Packaging', 'Moisture', ''],
                'detail_values' => ['Vacuum pack', 'Max 12%', ''],
                'image_file' => UploadedFile::fake()->image('arabica.jpg', 600, 400),
                'video_file' => UploadedFile::fake()->create('arabica.mp4', 1024, 'video/mp4'),
            ])
            ->assertRedirect(route('admin.products.index'));

        $product = Product::query()->where('slug', 'arabica-upload-test')->firstOrFail();

        $this->assertStringStartsWith('/storage/products/', $product->getRawOriginal('image_url'));
        $this->assertStringStartsWith('/storage/products/videos/', $product->getRawOriginal('video_url'));
        $this->assertStringContainsString('/media/products/', $product->image_url);
        $this->assertStringContainsString('/media/products/videos/', $product->video_url);
        $this->assertSame('Packaging', $product->product_details[0]['label']);
        $this->assertSame('Vacuum pack', $product->product_details[0]['value']);
        $this->assertCount(2, $product->product_details);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $product->getRawOriginal('image_url')));
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $product->getRawOriginal('video_url')));
    }

    public function test_media_url_converts_absolute_storage_urls_to_media_route(): void
    {
        config(['app.url' => 'https://agape153.com']);

        $this->assertSame(url('media/products/arabica%20roasted.jpg'), MediaUrl::public('https://agape153.com/storage/products/arabica roasted.jpg'));
    }

    public function test_admin_can_accept_order(): void
    {
        $admin = User::query()->create([
            'name' => 'Order Admin',
            'email' => 'order-admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $order = Order::query()->create([
            'order_number' => 'AGP-ACC-001',
            'customer_name' => 'Buyer',
            'customer_email' => 'buyer@example.com',
            'customer_phone' => '+62816795153',
            'country' => 'Indonesia',
            'shipping_address' => 'Jakarta',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->withSession(['admin_session_token' => 'test-token', 'admin_last_activity' => time()])
            ->patch(route('admin.orders.accept', $order))
            ->assertSessionHas('status');

        $order->refresh();

        $this->assertSame('confirmed', $order->status);
        $this->assertSame($admin->id, $order->accepted_by);
        $this->assertNotNull($order->accepted_at);
    }

    public function test_marketplace_shipping_completion_review_and_admin_reply_flow(): void
    {
        $bufferLevel = ob_get_level();

        Mail::fake();

        $admin = User::query()->create([
            'name' => 'Shipment Admin',
            'email' => 'shipment-admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $member = User::query()->create([
            'name' => 'Shipment Buyer',
            'email' => 'shipment-buyer@example.com',
            'password' => 'password',
            'role' => 'member',
            'status' => 'active',
        ]);

        $category = Category::query()->create([
            'name' => 'Shipment Coffee',
            'slug' => 'shipment-coffee',
            'type' => 'coffee',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Shipment Arabica',
            'slug' => 'shipment-arabica',
            'unit' => 'Kg',
            'price' => 100000,
            'currency' => 'IDR',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'order_number' => 'AGP-SHIP-001',
            'user_id' => $member->id,
            'customer_name' => 'Shipment Buyer',
            'customer_email' => 'shipment-buyer@example.com',
            'customer_phone' => '+62816795153',
            'country' => 'Indonesia',
            'shipping_address' => 'Jakarta',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'status' => 'confirmed',
            'shipping_status' => 'confirmed',
        ]);

        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Shipment Arabica',
            'quantity' => 1,
            'unit' => 'Kg',
            'unit_price' => 100000,
            'line_total' => 100000,
        ]);

        $this->actingAs($admin)
            ->withSession(['admin_session_token' => 'test-token', 'admin_last_activity' => time()])
            ->patch(route('admin.orders.shipment.update', $order), [
                'shipping_provider' => 'JNE',
                'tracking_code' => 'JNE123456',
                'shipping_status' => 'delivered',
                'shipping_notes' => 'Paket diterima security.',
            ])
            ->assertSessionHas('status');

        $order->refresh();

        $this->assertSame('shipped', $order->status);
        $this->assertSame('delivered', $order->shipping_status);
        $this->assertNotNull($order->delivered_at);

        $this->actingAs($member)
            ->patch(route('member.purchase-complete', $order))
            ->assertSessionHas('status');

        $this->assertSame('completed', $order->refresh()->status);

        $this->actingAs($member)
            ->post(route('member.product-review.store', $item), [
                'rating' => 5,
                'comment' => 'Produk rapi dan sesuai pesanan.',
            ])
            ->assertSessionHas('status');

        $review = ProductReview::query()->where('order_item_id', $item->id)->firstOrFail();

        $this->actingAs($admin)
            ->withSession(['admin_session_token' => 'test-token', 'admin_last_activity' => time()])
            ->patch(route('admin.reviews.reply', $review), [
                'admin_reply' => 'Terima kasih atas reviewnya.',
            ])
            ->assertSessionHas('status');

        $this->assertSame('Terima kasih atas reviewnya.', $review->refresh()->admin_reply);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'email',
            'event' => 'review.replied',
            'recipient' => 'shipment-buyer@example.com',
        ]);

        $this->get(route('products.show', $product))
            ->assertStatus(200)
            ->assertSee('Produk rapi dan sesuai pesanan.')
            ->assertSee('Terima kasih atas reviewnya.');

        while (ob_get_level() > $bufferLevel) {
            ob_end_clean();
        }
    }

    public function test_admin_can_update_stock_and_toggle_product_active_state(): void
    {
        $admin = User::query()->create([
            'name' => 'Stock Admin',
            'email' => 'stock-admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $category = Category::query()->create([
            'name' => 'Spices',
            'slug' => 'spices',
            'type' => 'spice',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Clove Stock Test',
            'slug' => 'clove-stock-test',
            'unit' => 'Kg',
            'price' => 100000,
            'currency' => 'IDR',
            'stock_quantity' => 3,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->withSession(['admin_session_token' => 'test-token', 'admin_last_activity' => time()])
            ->patch(route('admin.products.stock', $product), ['stock_quantity' => 24, 'reason' => 'Cycle count'])
            ->assertSessionHas('status');

        $this->assertSame(24, $product->refresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'quantity_before' => 3,
            'quantity_after' => 24,
            'reason' => 'Cycle count',
        ]);

        $this->actingAs($admin)
            ->withSession(['admin_session_token' => 'test-token', 'admin_last_activity' => time()])
            ->patch(route('admin.products.toggle-active', $product))
            ->assertSessionHas('status');

        $this->assertFalse($product->refresh()->is_active);
    }

    public function test_admin_can_download_sales_report_pdf(): void
    {
        $admin = User::query()->create([
            'name' => 'Report Admin',
            'email' => 'report-admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $order = Order::query()->create([
            'order_number' => 'AGP-RPT-001',
            'customer_name' => 'Report Buyer',
            'customer_email' => 'report@example.com',
            'customer_phone' => '+62816795153',
            'country' => 'Indonesia',
            'shipping_address' => 'Jakarta',
            'subtotal' => 120000,
            'total_amount' => 120000,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $order->items()->create([
            'product_name' => 'Arabica Report Test',
            'quantity' => 1,
            'unit' => 'Kg',
            'unit_price' => 120000,
            'line_total' => 120000,
        ]);

        $this->actingAs($admin)
            ->withSession(['admin_session_token' => 'test-token', 'admin_last_activity' => time()])
            ->get(route('admin.reports.pdf'))
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($admin)
            ->withSession(['admin_session_token' => 'test-token', 'admin_last_activity' => time()])
            ->get(route('admin.reports.csv'))
            ->assertStatus(200);
    }

    public function test_admin_can_update_appearance_settings(): void
    {
        Storage::fake('public');

        $admin = User::query()->create([
            'name' => 'Appearance Admin',
            'email' => 'appearance-admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->withSession(['admin_session_token' => 'test-token', 'admin_last_activity' => time()])
            ->put(route('admin.appearance.update'), [
                'primary_color' => '#047857',
                'accent_color' => '#facc15',
                'soft_color' => '#ecfdf5',
                'homepage_layout' => 'compact',
                'hero_badge' => 'Export-ready Indonesian commodities',
                'hero_title' => 'Agape153 Export',
                'hero_subtitle' => 'Custom homepage copy for international buyers.',
                'hero_image_url' => 'https://example.com/hero.jpg',
                'hero_image_file' => UploadedFile::fake()->image('hero.jpg', 1200, 800),
                'hero_slide_2_url' => 'https://example.com/hero-2.jpg',
                'hero_slide_3_url' => 'https://example.com/hero-3.jpg',
                'show_gallery' => 1,
                'google_client_id' => 'google-client-id.apps.googleusercontent.com',
                'google_client_secret' => 'google-client-secret',
                'google_redirect_uri' => 'http://127.0.0.1:8000/auth/google/callback',
            ])
            ->assertSessionHas('status');

        $this->assertSame('compact', WebsiteSetting::value('appearance_homepage_layout'));
        $this->assertSame('#047857', WebsiteSetting::value('appearance_primary_color'));
        $this->assertStringStartsWith('/storage/appearance/', WebsiteSetting::value('appearance_hero_image_url'));
        $this->assertSame('https://example.com/hero-2.jpg', WebsiteSetting::value('appearance_hero_slide_2_url'));
        $this->assertSame('google-client-id.apps.googleusercontent.com', WebsiteSetting::value('google_client_id'));
        $this->assertSame('google-client-secret', WebsiteSetting::value('google_client_secret'));
    }

    public function test_midtrans_notification_updates_payment_status(): void
    {
        config(['services.midtrans.server_key' => 'server-key-test']);

        $order = Order::query()->create([
            'order_number' => 'AGP-MID-001',
            'customer_name' => 'Midtrans Buyer',
            'customer_email' => 'midtrans@example.com',
            'customer_phone' => '+62816795153',
            'country' => 'Indonesia',
            'shipping_address' => 'Jakarta',
            'subtotal' => 150000,
            'total_amount' => 150000,
            'payment_method' => 'midtrans',
            'payment_gateway' => 'midtrans',
            'midtrans_order_id' => 'AGP-MID-001',
            'payment_status' => 'unpaid',
        ]);

        $payload = [
            'order_id' => 'AGP-MID-001',
            'status_code' => '200',
            'gross_amount' => '150000.00',
            'transaction_status' => 'settlement',
            'transaction_id' => 'trx-test-001',
        ];
        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].'server-key-test');

        $this->postJson(route('payments.midtrans.notification'), $payload)
            ->assertStatus(200);

        $order->refresh();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('trx-test-001', $order->midtrans_transaction_id);
        $this->assertNotNull($order->paid_at);
    }

    public function test_checkout_has_two_payment_buttons_and_whatsapp_redirect(): void
    {
        $member = User::query()->create([
            'name' => 'Checkout Buyer',
            'email' => 'checkout@example.com',
            'password' => 'password',
            'role' => 'member',
            'status' => 'active',
        ]);

        $category = Category::query()->create([
            'name' => 'Checkout Coffee',
            'slug' => 'checkout-coffee',
            'type' => 'coffee',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Checkout Robusta',
            'slug' => 'checkout-robusta',
            'unit' => 'Kg',
            'price' => 90000,
            'currency' => 'IDR',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $cart = [(string) $product->id => ['product_id' => $product->id, 'quantity' => 1]];

        $this->actingAs($member)
            ->withSession(['cart' => $cart])
            ->get(route('checkout.create'))
            ->assertStatus(200)
            ->assertSee('Bayar Online')
            ->assertSee('Konfirmasi via WhatsApp');

        $response = $this->actingAs($member)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.store'), [
                'customer_name' => 'Checkout Buyer',
                'customer_email' => 'checkout@example.com',
                'customer_phone' => '+62816795153',
                'country' => 'Indonesia',
                'shipping_address' => 'Jakarta',
                'payment_method' => 'whatsapp',
            ]);

        $response->assertStatus(302);
        $this->assertStringContainsString('https://wa.me/', $response->headers->get('Location'));
    }

    public function test_midtrans_client_success_marks_order_paid_and_shows_invoice(): void
    {
        $member = User::query()->create([
            'name' => 'Snap Buyer',
            'email' => 'snap@example.com',
            'password' => 'password',
            'role' => 'member',
            'status' => 'active',
        ]);

        $order = Order::query()->create([
            'order_number' => 'AGP-SNAP-001',
            'user_id' => $member->id,
            'customer_name' => 'Snap Buyer',
            'customer_email' => 'snap@example.com',
            'customer_phone' => '+62816795153',
            'country' => 'Indonesia',
            'shipping_address' => 'Jakarta',
            'subtotal' => 150000,
            'total_amount' => 150000,
            'payment_method' => 'midtrans',
            'payment_gateway' => 'midtrans',
            'midtrans_order_id' => 'AGP-SNAP-001',
            'payment_status' => 'unpaid',
        ]);

        $order->items()->create([
            'product_name' => 'Snap Arabica',
            'quantity' => 1,
            'unit' => 'Kg',
            'unit_price' => 150000,
            'line_total' => 150000,
        ]);

        $this->actingAs($member)
            ->postJson(route('checkout.midtrans-client-success', $order), [
                'transaction_id' => 'snap-client-trx',
                'transaction_status' => 'settlement',
            ])
            ->assertStatus(200)
            ->assertJsonPath('redirect_url', route('checkout.payment-success', $order));

        $this->assertSame('paid', $order->refresh()->payment_status);

        $this->actingAs($member)
            ->get(route('checkout.payment-success', $order))
            ->assertStatus(200)
            ->assertSee('Pembayaran Berhasil')
            ->assertSee('Download Invoice PDF');
    }

    public function test_language_switcher_sets_session_locale(): void
    {
        $this->post(route('language.switch', 'en'))
            ->assertSessionHas('locale', 'en');
    }
}
