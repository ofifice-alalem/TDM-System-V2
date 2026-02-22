<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceDiscountController;
use App\Http\Controllers\Admin\ProductPromotionController;
use App\Http\Controllers\Admin\AdminWithdrawalController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Shared\MainStockController;

Route::middleware(['web', 'auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
        Route::get('/{user}/details', [\App\Http\Controllers\Admin\MarketerDetailsController::class, 'show'])->name('details');
    });
    
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
        Route::get('/create', [\App\Http\Controllers\Shared\StoreController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Shared\StoreController::class, 'store'])->name('store');
        Route::get('/{store}', [\App\Http\Controllers\Shared\StoreController::class, 'show'])->name('show');
        Route::get('/{store}/edit', [\App\Http\Controllers\Shared\StoreController::class, 'edit'])->name('edit');
        Route::patch('/{store}', [\App\Http\Controllers\Shared\StoreController::class, 'update'])->name('update');
    });

    // Withdrawals Management
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [AdminWithdrawalController::class, 'index'])->name('index');
        Route::get('/{withdrawal}', [AdminWithdrawalController::class, 'show'])->name('show');
        Route::get('/{withdrawal}/pdf', [\App\Http\Controllers\Shared\Withdrawal\InvoiceController::class, 'generateWithdrawalInvoicePdf'])->name('pdf');
        Route::post('/{withdrawal}/approve', [AdminWithdrawalController::class, 'approve'])->name('approve');
        Route::post('/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject'])->name('reject');
    });

    // Main Stock
    Route::prefix('main-stock')->name('main-stock.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminMainStockController::class, 'index'])->name('index');
    });

    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ProductController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [\App\Http\Controllers\Admin\AdminProductController::class, 'edit'])->name('edit');
        Route::patch('/{product}', [\App\Http\Controllers\Admin\AdminProductController::class, 'update'])->name('update');
    });

    // Factory Invoices
    Route::prefix('factory-invoices')->name('factory-invoices.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminFactoryInvoiceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Shared\FactoryInvoiceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Shared\FactoryInvoiceController::class, 'store'])->name('store');
        Route::get('/{factoryInvoice}', [\App\Http\Controllers\Admin\AdminFactoryInvoiceController::class, 'show'])->name('show');
        Route::post('/{factoryInvoice}/document', [\App\Http\Controllers\Shared\FactoryInvoiceController::class, 'document'])->name('document');
        Route::post('/{factoryInvoice}/cancel', [\App\Http\Controllers\Shared\FactoryInvoiceController::class, 'cancel'])->name('cancel');
        Route::get('/{factoryInvoice}/pdf', [\App\Http\Controllers\Admin\AdminFactoryInvoiceController::class, 'pdf'])->name('pdf');
    });

    // Backups Management
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::post('/restore/{filename}', [BackupController::class, 'restore'])->name('restore');
        Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
        Route::delete('/{filename}', [BackupController::class, 'delete'])->name('delete');
    });

    // Statistics
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Shared\StatisticsController::class, 'index'])->name('index');
        Route::get('/marketer-stores/{marketer}', [\App\Http\Controllers\Shared\StatisticsController::class, 'getMarketerStores'])->name('marketer-stores');
    });
    
});
