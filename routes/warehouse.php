<?php

use App\Http\Controllers\Warehouse\WarehouseRequestController;
use App\Http\Controllers\Warehouse\WarehouseReturnController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [WarehouseRequestController::class, 'index'])->name('index');
            Route::patch('{id}/approve', [WarehouseRequestController::class, 'approve'])->name('approve');
            Route::patch('{id}/reject', [WarehouseRequestController::class, 'reject'])->name('reject');
            Route::post('{id}/document', [WarehouseRequestController::class, 'document'])->name('document');
            Route::get('{id}/documentation', [WarehouseRequestController::class, 'viewDocumentation'])->name('documentation');
            Route::get('{id}', [WarehouseRequestController::class, 'show'])->name('show');
        });

        Route::prefix('returns')->name('returns.')->group(function () {
            Route::get('/', [WarehouseReturnController::class, 'index'])->name('index');
            Route::patch('{id}/approve', [WarehouseReturnController::class, 'approve'])->name('approve');
            Route::patch('{id}/reject', [WarehouseReturnController::class, 'reject'])->name('reject');
            Route::post('{id}/document', [WarehouseReturnController::class, 'document'])->name('document');
            Route::get('{id}/documentation', [WarehouseReturnController::class, 'viewDocumentation'])->name('documentation');
            Route::get('{id}', [WarehouseReturnController::class, 'show'])->name('show');
        });

        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Warehouse\WarehouseSalesController::class, 'index'])->name('index');
            Route::get('{id}', [\App\Http\Controllers\Warehouse\WarehouseSalesController::class, 'show'])->name('show');
            Route::post('{id}/approve', [\App\Http\Controllers\Warehouse\WarehouseSalesController::class, 'approve'])->name('approve');
            Route::post('{id}/reject', [\App\Http\Controllers\Warehouse\WarehouseSalesController::class, 'reject'])->name('reject');
            Route::get('{id}/documentation', [\App\Http\Controllers\Warehouse\WarehouseSalesController::class, 'viewDocumentation'])->name('documentation');
        });

        Route::prefix('stores')->name('stores.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Shared\StoreController::class, 'index'])->name('index');
            Route::get('/{store}', [\App\Http\Controllers\Shared\StoreController::class, 'show'])->name('show');
        });

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Warehouse\WarehousePaymentController::class, 'index'])->name('index');
            Route::get('/{payment}', [\App\Http\Controllers\Warehouse\WarehousePaymentController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [\App\Http\Controllers\Warehouse\WarehousePaymentController::class, 'approve'])->name('approve');
            Route::patch('/{id}/reject', [\App\Http\Controllers\Warehouse\WarehousePaymentController::class, 'reject'])->name('reject');
        });

        Route::prefix('sales-returns')->name('sales-returns.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Warehouse\WarehouseSalesReturnController::class, 'index'])->name('index');
            Route::get('/{salesReturn}', [\App\Http\Controllers\Warehouse\WarehouseSalesReturnController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [\App\Http\Controllers\Warehouse\WarehouseSalesReturnController::class, 'approve'])->name('approve');
            Route::patch('/{id}/reject', [\App\Http\Controllers\Warehouse\WarehouseSalesReturnController::class, 'reject'])->name('reject');
            Route::get('/{id}/documentation', [\App\Http\Controllers\Warehouse\WarehouseSalesReturnController::class, 'viewDocumentation'])->name('documentation');
        });
    });
});
