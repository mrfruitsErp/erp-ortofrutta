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
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/order/{token}', [OrderPublicController::class, 'index']);
Route::post('/order/{token}', [OrderPublicController::class, 'store']);
Route::get('/order/{token}/ordine/{id}', [OrderPublicController::class, 'showOrder']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // CLIENTI / FORNITORI
    Route::resource('clients', ClientController::class);
    Route::resource('suppliers', SupplierController::class);

    // PRODOTTI
    Route::post('/products/massive-update', [ProductController::class, 'massiveUpdate'])
        ->name('products.massive-update');
    Route::patch('/products/{product}/inline-update', [ProductController::class, 'inlineUpdate'])
        ->name('products.inline-update');

    Route::get('/products/export', [ProductController::class, 'export'])
        ->name('products.export');
    Route::post('/products/import', [ProductController::class, 'import'])
        ->name('products.import');

    Route::resource('products', ProductController::class);

    // DOCUMENTI
    Route::resource('documents', DocumentController::class);

    Route::get('/documents/{id}/pdf', [DocumentController::class, 'pdf'])
        ->name('documents.pdf');
    Route::post('/save-real-weight', [DocumentController::class, 'saveRealWeight'])
        ->name('save.real.weight');
    Route::get('/documents/{id}/assign-number', [DocumentController::class, 'assignDdtNumber'])
        ->name('documents.assignNumber');
    Route::get('/documents/{id}/cancel', [DocumentController::class, 'cancelDdt'])
        ->name('documents.cancel');

    // ORDINI
    Route::get('/orders/{order}/confirm', [OrderController::class, 'confirmOrder'])
        ->name('orders.confirm');
    Route::get('/orders/{order}/generate-document', [OrderController::class, 'generateDocument'])
        ->name('orders.generateDocument');

    Route::get('/orders/print', [OrderController::class, 'printView'])
        ->name('orders.print');
    Route::get('/orders/export', [OrderController::class, 'exportOrders'])
        ->name('orders.export');
    Route::get('/orders/export-items', [OrderController::class, 'exportOrderItems'])
        ->name('orders.export-items');
    Route::get('/orders/export-product-summary', [OrderController::class, 'exportProductSummary'])
        ->name('orders.export-product-summary');
    Route::post('/orders/massive-action', [OrderController::class, 'massiveAction'])
        ->name('orders.massive-action');

    Route::resource('orders', OrderController::class);

    // SETTINGS
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

    // ACQUISTI
    Route::resource('purchases', PurchaseController::class);

    // PAGAMENTI
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/crediti', [PaymentController::class, 'crediti'])->name('crediti.index');

    // MAGAZZINO
    Route::get('/magazzino', [StockController::class, 'index'])->name('magazzino.index');
    Route::get('/carico-magazzino', [StockController::class, 'create'])->name('carico.magazzino');
    Route::post('/stock/bulk', [StockController::class, 'bulkStore'])->name('stock.bulk.store');
    Route::post('/stock', [StockController::class, 'store'])->name('stock.store');

    Route::get('/movimenti-magazzino', [StockMovementController::class, 'index'])
        ->name('movimenti.index');

    // 🔥 REPORT
    Route::get('/report/prodotti', [ReportController::class, 'prodotti'])->name('report.prodotti');
    Route::get('/report/clienti', [ReportController::class, 'clienti'])->name('report.clienti');

});

/*
|--------------------------------------------------------------------------
| UTILITY
|--------------------------------------------------------------------------
*/

Route::get('/run-migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'MIGRATION COMPLETATE';
});

require __DIR__.'/auth.php';