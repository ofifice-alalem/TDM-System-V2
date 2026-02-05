<?php

use App\Http\Controllers\Warehouse\WarehouseRequestController;
use Illuminate\Support\Facades\Route;

// Temporary: Simulate logged-in warehouse keeper (ID=2)
Route::middleware(['web'])->group(function () {
    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [WarehouseRequestController::class, 'index'])->name('index');
            Route::get('/{request}', [WarehouseRequestController::class, 'show'])->name('show');
            Route::patch('/{request}/approve', [WarehouseRequestController::class, 'approve'])->name('approve');
            Route::patch('/{request}/reject', [WarehouseRequestController::class, 'reject'])->name('reject');
            Route::patch('/{request}/document', [WarehouseRequestController::class, 'document'])->name('document');
        });
    });
});
