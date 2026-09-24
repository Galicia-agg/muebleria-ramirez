<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\StockEntryController as AdminStockEntryController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Shop\AddressController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\OrderController as ShopOrderController;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalogo', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/productos/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito', [CartController::class, 'store'])->name('cart.store');
Route::patch('/carrito/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/mis-direcciones', [AddressController::class, 'index'])->name('shop.addresses.index');
    Route::post('/mis-direcciones', [AddressController::class, 'store'])->name('shop.addresses.store');
    Route::put('/mis-direcciones/{address}', [AddressController::class, 'update'])->name('shop.addresses.update');
    Route::delete('/mis-direcciones/{address}', [AddressController::class, 'destroy'])->name('shop.addresses.destroy');

    Route::get('/mis-pedidos', [ShopOrderController::class, 'index'])->name('shop.orders.index');
    Route::get('/mis-pedidos/{order}', [ShopOrderController::class, 'show'])->name('shop.orders.show');

    Route::get('/checkout/success/{orderNumber}', function (string $orderNumber, Request $request) {
        $order = Order::query()->where('order_number', $orderNumber)->firstOrFail();
        abort_unless($order->user_id === $request->user()->id, 403);

        return redirect()->route('shop.orders.show', $order->id)->with('success', 'Pago en línea completado.');
    })->name('checkout.success');

    Route::get('/checkout/cancelled/{orderNumber}', function (string $orderNumber, Request $request) {
        $order = Order::query()->where('order_number', $orderNumber)->firstOrFail();
        abort_unless($order->user_id === $request->user()->id, 403);

        return redirect()->route('shop.orders.show', $order->id)->with('warning', 'El pago en línea fue cancelado.');
    })->name('checkout.cancelled');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.products.index');
        })->name('dashboard');

        Route::resource('categories', AdminCategoryController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::resource('products', AdminProductController::class)
            ->except(['show']);
        Route::delete('/products/images/{productImage}', [AdminProductController::class, 'destroyImage'])
            ->name('products.images.destroy');
        Route::post('/products/{product}/images/reorder', [AdminProductController::class, 'reorderImages'])
            ->name('products.images.reorder');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

        Route::resource('users', AdminUserController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::resource('suppliers', AdminSupplierController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::resource('stock-entries', AdminStockEntryController::class)
            ->only(['index', 'create', 'store']);
    });
});

Route::post('/webhooks/recurrente', function (Request $request, PaymentService $payments) {
    $payments->handleOnlineWebhook($request->all(), $request->headers->all());

    return response()->noContent();
})->name('webhooks.recurrente');
