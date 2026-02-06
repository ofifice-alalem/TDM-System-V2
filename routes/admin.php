<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceDiscountController;

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Invoice Discounts
    Route::prefix('discounts')->name('discounts.')->group(function () {
        Route::get('/', [InvoiceDiscountController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceDiscountController::class, 'create'])->name('create');
        Route::post('/', [InvoiceDiscountController::class, 'store'])->name('store');
        Route::patch('/{discount}/toggle', [InvoiceDiscountController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{discount}', [InvoiceDiscountController::class, 'destroy'])->name('destroy');
    });
    
});
