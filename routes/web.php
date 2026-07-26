<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\AppearanceController as AdminAppearanceController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CmsController as AdminCmsController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\NotificationLogController as AdminNotificationLogController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductImportExportController as AdminProductImportExportController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Admin\TranslationController as AdminTranslationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/about')->name('home');
Route::post('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*')->name('media.public');
Route::get('/about', AboutController::class)->name('about');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:6,1')->name('contact.store');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/payments/midtrans/finish', [PaymentController::class, 'midtransFinish'])->name('payments.midtrans.finish');
Route::post('/payments/midtrans/notification', [PaymentController::class, 'midtransNotification'])->name('payments.midtrans.notification');
Route::get('/order-tracking', [CheckoutController::class, 'trackForm'])->name('orders.track');
Route::post('/order-tracking', [CheckoutController::class, 'track'])->name('orders.track.search');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::post('/register/whatsapp', [AuthController::class, 'registerWithWhatsApp'])->middleware('throttle:6,1')->name('register.whatsapp');
    Route::get('/register/whatsapp/verify', [AuthController::class, 'showWhatsAppVerification'])->name('register.whatsapp.verify.form');
    Route::post('/register/whatsapp/verify', [AuthController::class, 'verifyWhatsAppRegistration'])->middleware('throttle:8,1')->name('register.whatsapp.verify');
    Route::post('/register/whatsapp/resend', [AuthController::class, 'resendWhatsAppOtp'])->middleware('throttle:3,1')->name('register.whatsapp.resend');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware('throttle:5,1')->name('admin.login.store');
Route::post('/admin/logout', [AuthController::class, 'adminLogout'])->middleware('admin')->name('admin.logout');

Route::middleware('auth')->group(function (): void {
    Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/payment-success/{order}', [CheckoutController::class, 'paymentSuccess'])->name('checkout.payment-success');
    Route::post('/checkout/success/{order}/pay-midtrans', [CheckoutController::class, 'payWithMidtrans'])->name('checkout.pay-midtrans');
    Route::post('/checkout/success/{order}/midtrans-client-success', [CheckoutController::class, 'midtransClientSuccess'])->name('checkout.midtrans-client-success');
    Route::get('/member', [MemberController::class, 'dashboard'])->name('member.dashboard');
    Route::get('/member/purchase-history', [MemberController::class, 'purchaseHistory'])->name('member.purchase-history');
    Route::get('/member/purchase-history/{order}', [MemberController::class, 'purchaseDetail'])->name('member.purchase-detail');
    Route::patch('/member/purchase-history/{order}/complete', [MemberController::class, 'completeOrder'])->name('member.purchase-complete');
    Route::get('/member/purchase-history/{order}/invoice.pdf', [MemberController::class, 'downloadInvoice'])->name('member.purchase-invoice');
    Route::post('/member/purchase-items/{item}/review', [MemberController::class, 'storeReview'])->name('member.product-review.store');
    Route::get('/member/profile', [MemberController::class, 'profile'])->name('member.profile');
    Route::patch('/member/profile', [MemberController::class, 'updateProfile'])->name('member.profile.update');
    Route::patch('/member/password', [MemberController::class, 'updatePassword'])->name('member.password.update');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('reports', [AdminReportController::class, 'index'])->middleware('permission:view-reports')->name('reports.index');
    Route::get('analytics', [AdminAnalyticsController::class, 'index'])->middleware('permission:view-analytics')->name('analytics.index');
    Route::get('customers', [AdminCustomerController::class, 'index'])->middleware('permission:manage-orders')->name('customers.index');
    Route::get('customers/{customer}', [AdminCustomerController::class, 'show'])->middleware('permission:manage-orders')->name('customers.show');
    Route::get('stock-movements', [AdminInventoryController::class, 'movements'])->middleware('permission:manage-products')->name('stock-movements.index');
    Route::get('notification-logs', [AdminNotificationLogController::class, 'index'])->middleware('permission:manage-messages')->name('notification-logs.index');
    Route::get('reports/sales.pdf', [AdminReportController::class, 'downloadPdf'])->middleware('permission:view-reports')->name('reports.pdf');
    Route::get('reports/sales.csv', [AdminReportController::class, 'downloadCsv'])->middleware('permission:view-reports')->name('reports.csv');
    Route::get('appearance', [AdminAppearanceController::class, 'edit'])->middleware('permission:manage-cms')->name('appearance.edit');
    Route::put('appearance', [AdminAppearanceController::class, 'update'])->middleware('permission:manage-cms')->name('appearance.update');
    Route::get('cms', [AdminCmsController::class, 'index'])->middleware('permission:manage-cms')->name('cms.index');
    Route::post('cms/sections', [AdminCmsController::class, 'storeSection'])->middleware('permission:manage-cms')->name('cms.sections.store');
    Route::put('cms/sections/{section}', [AdminCmsController::class, 'updateSection'])->middleware('permission:manage-cms')->name('cms.sections.update');
    Route::post('cms/faqs', [AdminCmsController::class, 'storeFaq'])->middleware('permission:manage-cms')->name('cms.faqs.store');
    Route::put('cms/faqs/{faq}', [AdminCmsController::class, 'updateFaq'])->middleware('permission:manage-cms')->name('cms.faqs.update');
    Route::post('cms/galleries', [AdminCmsController::class, 'storeGallery'])->middleware('permission:manage-cms')->name('cms.galleries.store');
    Route::put('cms/galleries/{gallery}', [AdminCmsController::class, 'updateGallery'])->middleware('permission:manage-cms')->name('cms.galleries.update');
    Route::post('cms/testimonials', [AdminCmsController::class, 'storeTestimonial'])->middleware('permission:manage-cms')->name('cms.testimonials.store');
    Route::put('cms/testimonials/{testimonial}', [AdminCmsController::class, 'updateTestimonial'])->middleware('permission:manage-cms')->name('cms.testimonials.update');
    Route::post('cms/news', [AdminCmsController::class, 'storeNews'])->middleware('permission:manage-cms')->name('cms.news.store');
    Route::put('cms/news/{newsPost}', [AdminCmsController::class, 'updateNews'])->middleware('permission:manage-cms')->name('cms.news.update');
    Route::put('cms/footer', [AdminCmsController::class, 'updateFooter'])->middleware('permission:manage-cms')->name('cms.footer.update');
    Route::patch('orders/{order}/accept', [AdminOrderController::class, 'accept'])->middleware('permission:manage-orders')->name('orders.accept');
    Route::patch('orders/{order}/shipment', [AdminOrderController::class, 'updateShipment'])->middleware('permission:manage-orders')->name('orders.shipment.update');
    Route::get('orders/{order}/shipping-label', [AdminOrderController::class, 'shippingLabel'])->middleware('permission:manage-orders')->name('orders.shipping-label');
    Route::patch('reviews/{review}/reply', [AdminOrderController::class, 'replyReview'])->middleware('permission:manage-orders')->name('reviews.reply');
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update'])->middleware('permission:manage-orders');
    Route::patch('products/{product}/stock', [AdminProductController::class, 'updateStock'])->middleware('permission:manage-products')->name('products.stock');
    Route::patch('products/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->middleware('permission:manage-products')->name('products.toggle-active');
    Route::get('products-export.csv', [AdminProductImportExportController::class, 'export'])->middleware('permission:manage-products')->name('products.export');
    Route::post('products-import', [AdminProductImportExportController::class, 'import'])->middleware('permission:manage-products')->name('products.import');
    Route::get('products-trash', [AdminProductController::class, 'trash'])->middleware('permission:manage-products')->name('products.trash');
    Route::patch('products-trash/{product}/restore', [AdminProductController::class, 'restore'])->middleware('permission:manage-products')->name('products.restore');
    Route::get('categories-trash', [AdminCategoryController::class, 'trash'])->middleware('permission:manage-categories')->name('categories.trash');
    Route::patch('categories-trash/{category}/restore', [AdminCategoryController::class, 'restore'])->middleware('permission:manage-categories')->name('categories.restore');
    Route::resource('categories', AdminCategoryController::class)->except('show')->middleware('permission:manage-categories');
    Route::post('contact-messages/{contactMessage}/reply', [AdminContactMessageController::class, 'reply'])->middleware('permission:manage-messages')->name('contact-messages.reply');
    Route::resource('contact-messages', AdminContactMessageController::class)->only(['index', 'show', 'update', 'destroy'])->middleware('permission:manage-messages');
    Route::resource('products', AdminProductController::class)->except('show')->middleware('permission:manage-products');
    Route::get('staff', [AdminStaffController::class, 'index'])->middleware('permission:manage-users')->name('staff.index');
    Route::post('staff/users', [AdminStaffController::class, 'storeUser'])->middleware('permission:manage-users')->name('staff.users.store');
    Route::put('staff/users/{user}', [AdminStaffController::class, 'updateUser'])->middleware('permission:manage-users')->name('staff.users.update');
    Route::put('staff/roles/{role}', [AdminStaffController::class, 'updateRole'])->middleware('permission:manage-users')->name('staff.roles.update');
    Route::get('translations', [AdminTranslationController::class, 'index'])->middleware('permission:manage-cms')->name('translations.index');
    Route::post('translations', [AdminTranslationController::class, 'store'])->middleware('permission:manage-cms')->name('translations.store');
});

Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml')."\n", 200, [
        'Content-Type' => 'text/plain',
    ]);
});

Route::get('/sitemap.xml', function () {
    return response()->view('sitemap', [
        'products' => Product::query()->active()->select('slug', 'updated_at')->get(),
        'categories' => Category::query()->active()->select('slug', 'updated_at')->get(),
    ])->header('Content-Type', 'application/xml');
})->name('sitemap');
