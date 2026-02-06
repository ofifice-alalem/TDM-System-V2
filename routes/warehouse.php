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
    });
});
