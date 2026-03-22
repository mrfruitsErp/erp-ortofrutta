<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderPublicController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DeliveryZoneController;
use App\Http\Controllers\DeliverySlotController;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| ORDINI CLIENTE DA LINK (PUBBLICO)
|--------------------------------------------------------------------------
*/

Route::get('/order/{token}', [OrderPublicController::class, 'index']);
Route::post('/order/{token}', [OrderPublicController::class, 'store']);
Route::get('/order/{token}/ordine/{id}', [OrderPublicController::class, 'showOrder']);

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| ERP ROUTES PROTETTE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::resource('clients', ClientController::class);
    Route::resource('suppliers', SupplierController::class);

    Route::post('/products/massive-update', [ProductController::class, 'massiveUpdate'])
        ->name('products.massive-update');

    Route::resource('products', ProductController::class);

    Route::resource('documents', DocumentController::class);

    Route::get('/documents/{id}/pdf', [DocumentController::class, 'pdf'])
        ->name('documents.pdf');

    Route::post('/save-real-weight', [DocumentController::class, 'saveRealWeight'])
        ->name('save.real.weight');

    Route::get('/documents/{id}/assign-number', [DocumentController::class, 'assignDdtNumber'])
        ->name('documents.assignNumber');

    Route::get('/documents/{id}/cancel', [DocumentController::class, 'cancelDdt'])
        ->name('documents.cancel');

    Route::get('/orders/{order}/confirm', [OrderController::class, 'confirmOrder'])
        ->name('orders.confirm');

    Route::get('/orders/{order}/generate-document', [OrderController::class, 'generateDocument'])
        ->name('orders.generateDocument');

    Route::resource('orders', OrderController::class);

    Route::get('/settings/orders', [SettingsController::class, 'orders']);
    Route::post('/settings/orders', [SettingsController::class, 'saveOrders']);

    Route::get('/settings/delivery-zones', [DeliveryZoneController::class, 'index']);
    Route::post('/settings/delivery-zones', [DeliveryZoneController::class, 'store']);
    Route::post('/settings/delivery-zones/{id}', [DeliveryZoneController::class, 'update']);
    Route::delete('/settings/delivery-zones/{id}', [DeliveryZoneController::class, 'destroy']);

    Route::get('/settings/delivery-slots', [DeliverySlotController::class, 'index']);
    Route::post('/settings/delivery-slots', [DeliverySlotController::class, 'store']);
    Route::post('/settings/delivery-slots/{id}', [DeliverySlotController::class, 'update']);
    Route::delete('/settings/delivery-slots/{id}', [DeliverySlotController::class, 'destroy']);

    Route::resource('purchases', PurchaseController::class);

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/crediti', [PaymentController::class, 'crediti'])->name('crediti.index');

    Route::get('/magazzino', [StockController::class, 'index'])->name('magazzino.index');
    Route::get('/carico-magazzino', [StockController::class, 'create'])->name('carico.magazzino');

    Route::post('/stock/bulk', [StockController::class, 'bulkStore'])->name('stock.bulk.store');
    Route::post('/stock', [StockController::class, 'store'])->name('stock.store');

    Route::get('/movimenti-magazzino', [StockMovementController::class, 'index'])
        ->name('movimenti.index');
});

/*
|--------------------------------------------------------------------------
| MIGRATE (TEMPORANEO)
|--------------------------------------------------------------------------
*/

Route::get('/run-migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'MIGRATION COMPLETATE';
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
use Illuminate\Support\Facades\Hash;
use App\Models\User;

