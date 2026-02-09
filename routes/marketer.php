<?php

use App\Http\Controllers\Marketer\MarketerRequestController;
use App\Http\Controllers\Marketer\MarketerReturnController;
use App\Http\Controllers\Marketer\MarketerStockController;
use App\Http\Controllers\Marketer\SalesController;
use Illuminate\Support\Facades\Route;

// Temporary: Simulate logged-in marketer (ID=3)
Route::middleware(['web'])->group(function () {
    Route::prefix('marketer')->name('marketer.')->group(function () {
        Route::get('/stock', [MarketerStockController::class, 'index'])->name('stock.index');
        
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [MarketerRequestController::class, 'index'])->name('index');
            Route::get('/create', [MarketerRequestController::class, 'create'])->name('create');
            Route::post('/', [MarketerRequestController::class, 'store'])->name('store');
            Route::get('/{request}', [MarketerRequestController::class, 'show'])->name('show');
            Route::get('/{request}/pdf', [MarketerRequestController::class, 'pdf'])->name('pdf');
            Route::patch('/{marketerRequest}/cancel', [MarketerRequestController::class, 'cancel'])->name('cancel');
        });

        Route::prefix('returns')->name('returns.')->group(function () {
            Route::get('/', [MarketerReturnController::class, 'index'])->name('index');
            Route::get('/create', [MarketerReturnController::class, 'create'])->name('create');
            Route::post('/', [MarketerReturnController::class, 'store'])->name('store');
            Route::get('/{return}', [MarketerReturnController::class, 'show'])->name('show');
            Route::get('/{return}/pdf', [MarketerReturnController::class, 'pdf'])->name('pdf');
            Route::patch('/{return}/cancel', [MarketerReturnController::class, 'cancel'])->name('cancel');
        });

        Route::prefix('discounts')->name('discounts.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Marketer\DiscountController::class, 'index'])->name('index');
        });

        Route::prefix('promotions')->name('promotions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Marketer\PromotionController::class, 'index'])->name('index');
        });

        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/', [SalesController::class, 'index'])->name('index');
            Route::get('/create', [SalesController::class, 'create'])->name('create');
            Route::post('/', [SalesController::class, 'store'])->name('store');
            Route::get('/{sale}', [SalesController::class, 'show'])->name('show');
            Route::delete('/{sale}/cancel', [SalesController::class, 'cancel'])->name('cancel');
            Route::get('/{sale}/pdf', [\App\Http\Controllers\Shared\Sales\InvoiceController::class, 'generateSalesInvoicePdf'])->name('pdf');
        });
    });
});
