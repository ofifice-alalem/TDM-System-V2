<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceDiscountController;
use App\Http\Controllers\Admin\ProductPromotionController;

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
    
});
