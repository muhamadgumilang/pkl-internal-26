<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// =======================
// CONTROLLERS (PUBLIC)
// =======================
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;

// =======================
// CONTROLLERS (ADMIN)
// =======================
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;


// =======================
// CONTROLLERS (AUTH)
// =======================
use App\Http\Controllers\Auth\GoogleController;

//
// =======================
// HALAMAN PUBLIK
// =======================
//
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

//
// =======================
// HALAMAN CUSTOMER (LOGIN)
// =======================
//
Route::middleware('auth')->group(function () {

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');

    // Wishlist
    // Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    // Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//
// =======================
// HALAMAN ADMIN
// =======================
//
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::resource('products', AdminProductController::class);
        Route::resource('categories', AdminCategoryController::class);

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('orders.updateStatus');
});

//
// =======================
// AUTH
// =======================
Auth::routes();

// Google Login
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');


Route::middleware('auth')->group(function() {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// routes/web.php (HAPUS SETELAH TESTING!)

// use App\Services\MidtransService;

// Route::get('/debug-midtrans', function () {
//     // Cek apakah config terbaca
//     $config = [
//         'merchant_id'   => config('midtrans.merchant_id'),
//         'client_key'    => config('midtrans.client_key'),
//         'server_key'    => config('midtrans.server_key') ? '***SET***' : 'NOT SET',
//         'is_production' => config('midtrans.is_production'),
//     ];

//     // Test buat dummy token
//     try {
//         $service = new MidtransService();

//         // Buat dummy order untuk testing
//         $dummyOrder = new \App\Models\Order();
//         $dummyOrder->order_number = 'TEST-' . time();
//         $dummyOrder->total_amount = 10000;
//         $dummyOrder->shipping_cost = 0;
//         $dummyOrder->shipping_name = 'Test User';
//         $dummyOrder->shipping_phone = '08123456789';
//         $dummyOrder->shipping_address = 'Jl. Test No. 123';
//         $dummyOrder->user = (object) [
//             'name'  => 'Tester',
//             'email' => 'test@example.com',
//             'phone' => '08123456789',
//         ];
//         // Dummy items
//         $dummyOrder->items = collect([
//             (object) [
//                 'product_id'   => 1,
//                 'product_name' => 'Produk Test',
//                 'price'        => 10000,
//                 'quantity'     => 1,
//             ],
//         ]);

//         $token = $service->createSnapToken($dummyOrder);

//         return response()->json([
//             'status'  => 'SUCCESS',
//             'message' => 'Berhasil terhubung ke Midtrans!',
//             'config'  => $config,
//             'token'   => $token,
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => 'ERROR',
//             'message' => $e->getMessage(),
//             'config'  => $config,
//         ], 500);
//     }
// });

// routes/web.php

use App\Http\Controllers\PaymentController;

Route::middleware('auth')->group(function () {
    // ... routes lainnya

    // Payment Routes
    Route::get('/orders/{order}/pay', [PaymentController::class, 'show'])
        ->name('orders.pay');
    Route::get('/orders/{order}/success', [PaymentController::class, 'success'])
        ->name('orders.success');
    Route::get('/orders/{order}/pending', [PaymentController::class, 'pending'])
        ->name('orders.pending');
});

// routes/web.php

use App\Http\Controllers\MidtransNotificationController;

// ============================================================
// MIDTRANS WEBHOOK
// Route ini HARUS public (tanpa auth middleware)
// Karena diakses oleh SERVER Midtrans, bukan browser user
// ============================================================
Route::post('midtrans/notification', [MidtransNotificationController::class, 'handle'])
    ->name('midtrans.notification');