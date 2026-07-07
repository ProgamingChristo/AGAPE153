<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/order-tracking', [CheckoutController::class, 'trackForm'])->name('orders.track');
Route::post('/order-tracking', [CheckoutController::class, 'track'])->name('orders.track.search');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/member', [MemberController::class, 'dashboard'])->name('member.dashboard');
    Route::get('/member/profile', [MemberController::class, 'profile'])->name('member.profile');
    Route::patch('/member/profile', [MemberController::class, 'updateProfile'])->name('member.profile.update');
    Route::patch('/member/password', [MemberController::class, 'updatePassword'])->name('member.password.update');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::resource('categories', AdminCategoryController::class)->except('show');
    Route::resource('products', AdminProductController::class)->except('show');
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
