<?php

use App\Http\Controllers\Marketer\MarketerRequestController;
use App\Http\Controllers\Marketer\MarketerReturnController;
use App\Http\Controllers\Marketer\MarketerStockController;
use App\Http\Controllers\Marketer\SalesController;
use App\Http\Controllers\Marketer\CommissionController;
use App\Http\Controllers\Marketer\WithdrawalController;
use App\Http\Controllers\Shared\MainStockController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:marketer'])->group(function () {
    Route::prefix('marketer')->name('marketer.')->group(function () {
        Route::get('/stock', [MarketerStockController::class, 'index'])->name('stock.index');
        
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [MarketerRequestController::class, 'index'])->name('index');
            Route::get('/create', [MarketerRequestController::class, 'create'])->name('create');
            Route::post('/', [MarketerRequestController::class, 'store'])->name('store');
            Route::get('/{request}', [MarketerRequestController::class, 'show'])->name('show');
            Route::get('/{request}/pdf', [MarketerRequestController::class, 'pdf'])->name('pdf');
            Route::get('/{request}/documentation', [MarketerRequestController::class, 'viewDocumentation'])->name('documentation');
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

        Route::prefix('stores')->name('stores.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Shared\StoreController::class, 'index'])->name('index');
            Route::get('/{store}', [\App\Http\Controllers\Shared\StoreController::class, 'show'])->name('show');
        });

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Marketer\PaymentController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Marketer\PaymentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Marketer\PaymentController::class, 'store'])->name('store');
            Route::get('/{payment}', [\App\Http\Controllers\Marketer\PaymentController::class, 'show'])->name('show');
            Route::get('/{payment}/pdf', [\App\Http\Controllers\Shared\Payment\InvoiceController::class, 'generatePaymentInvoicePdf'])->name('pdf');
            Route::patch('/{payment}/cancel', [\App\Http\Controllers\Marketer\PaymentController::class, 'cancel'])->name('cancel');
            Route::get('/store/{storeId}/debt', [\App\Http\Controllers\Marketer\PaymentController::class, 'getStoreDebt'])->name('store.debt');
        });

        Route::prefix('commissions')->name('commissions.')->group(function () {
            Route::get('/', [CommissionController::class, 'index'])->name('index');
        });

        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('/', [WithdrawalController::class, 'index'])->name('index');
            Route::get('/create', [WithdrawalController::class, 'create'])->name('create');
            Route::post('/', [WithdrawalController::class, 'store'])->name('store');
            Route::get('/{withdrawal}', [WithdrawalController::class, 'show'])->name('show');
            Route::get('/{withdrawal}/pdf', [\App\Http\Controllers\Shared\Withdrawal\InvoiceController::class, 'generateWithdrawalInvoicePdf'])->name('pdf');
            Route::patch('/{withdrawal}/cancel', [WithdrawalController::class, 'cancel'])->name('cancel');
        });

        Route::prefix('sales-returns')->name('sales-returns.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Marketer\SalesReturnController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Marketer\SalesReturnController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Marketer\SalesReturnController::class, 'store'])->name('store');
            Route::get('/{salesReturn}', [\App\Http\Controllers\Marketer\SalesReturnController::class, 'show'])->name('show');
            Route::patch('/{salesReturn}/cancel', [\App\Http\Controllers\Marketer\SalesReturnController::class, 'cancel'])->name('cancel');
            Route::get('/{salesReturn}/pdf', [\App\Http\Controllers\Shared\SalesReturn\InvoiceController::class, 'generateSalesReturnInvoicePdf'])->name('pdf');
        });

        Route::prefix('main-stock')->name('main-stock.')->group(function () {
            Route::get('/', [MainStockController::class, 'index'])->name('index');
        });
    });
});
