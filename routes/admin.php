<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceDiscountController;
use App\Http\Controllers\Admin\ProductPromotionController;
use App\Http\Controllers\Admin\AdminWithdrawalController;

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Invoice Discounts
    Route::prefix('discounts')->name('discounts.')->group(function () {
        Route::get('/', [InvoiceDiscountController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceDiscountController::class, 'create'])->name('create');
        Route::post('/', [InvoiceDiscountController::class, 'store'])->name('store');
        Route::patch('/{discount}/toggle', [InvoiceDiscountController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{discount}', [InvoiceDiscountController::class, 'destroy'])->name('destroy');
    });
    
    // Product Promotions
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [ProductPromotionController::class, 'index'])->name('index');
        Route::get('/create', [ProductPromotionController::class, 'create'])->name('create');
        Route::post('/', [ProductPromotionController::class, 'store'])->name('store');
        Route::patch('/{promotion}/toggle', [ProductPromotionController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{promotion}', [ProductPromotionController::class, 'destroy'])->name('destroy');
    });

    // Stores Management
    Route::prefix('stores')->name('stores.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Shared\StoreController::class, 'index'])->name('index');
        Route::get('/{store}', [\App\Http\Controllers\Shared\StoreController::class, 'show'])->name('show');
    });

    // Withdrawals Management
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [AdminWithdrawalController::class, 'index'])->name('index');
        Route::get('/{withdrawal}', [AdminWithdrawalController::class, 'show'])->name('show');
        Route::get('/{withdrawal}/pdf', [\App\Http\Controllers\Shared\Withdrawal\InvoiceController::class, 'generateWithdrawalInvoicePdf'])->name('pdf');
        Route::post('/{withdrawal}/approve', [AdminWithdrawalController::class, 'approve'])->name('approve');
        Route::post('/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject'])->name('reject');
    });
    
});
