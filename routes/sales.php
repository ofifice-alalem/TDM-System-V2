<?php

use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\CustomerInvoiceController;
use App\Http\Controllers\Sales\CustomerPaymentController;
use App\Http\Controllers\Sales\CustomerReturnController;
use App\Http\Controllers\Sales\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:sales'])->prefix('sales')->name('sales.')->group(function () {
    
    // Statistics
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/statistics/quick-invoices', [StatisticsController::class, 'quickInvoices'])->name('statistics.quick-invoices');
    Route::get('/statistics/quick-payments', [StatisticsController::class, 'quickPayments'])->name('statistics.quick-payments');
    Route::get('/statistics/quick-returns', [StatisticsController::class, 'quickReturns'])->name('statistics.quick-returns');
    Route::get('/statistics/quick-summary', [StatisticsController::class, 'quickSummary'])->name('statistics.quick-summary');
    
    // Customers
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::patch('/{customer}', [CustomerController::class, 'update'])->name('update');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [CustomerInvoiceController::class, 'index'])->name('index');
        Route::get('/create', [CustomerInvoiceController::class, 'create'])->name('create');
        Route::post('/', [CustomerInvoiceController::class, 'store'])->name('store');
        Route::get('/search-customers', [CustomerInvoiceController::class, 'searchCustomers'])->name('search.customers');
        Route::get('/{invoice}', [CustomerInvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/pdf', [CustomerInvoiceController::class, 'pdf'])->name('pdf');
        Route::delete('/{invoice}/cancel', [CustomerInvoiceController::class, 'cancel'])->name('cancel');
    });

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [CustomerPaymentController::class, 'index'])->name('index');
        Route::get('/create', [CustomerPaymentController::class, 'create'])->name('create');
        Route::post('/', [CustomerPaymentController::class, 'store'])->name('store');
        Route::get('/search-customers', [CustomerPaymentController::class, 'searchCustomers'])->name('search.customers');
        Route::get('/customer/{customerId}/debt', [CustomerPaymentController::class, 'getCustomerDebt'])->name('customer.debt');
        Route::get('/{payment}', [CustomerPaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/pdf', [CustomerPaymentController::class, 'pdf'])->name('pdf');
        Route::delete('/{payment}/cancel', [CustomerPaymentController::class, 'cancel'])->name('cancel');
    });

    // Returns
    Route::prefix('returns')->name('returns.')->group(function () {
        Route::get('/', [CustomerReturnController::class, 'index'])->name('index');
        Route::get('/create', [CustomerReturnController::class, 'create'])->name('create');
        Route::post('/', [CustomerReturnController::class, 'store'])->name('store');
        Route::get('/search-invoices', [CustomerReturnController::class, 'searchInvoices'])->name('search.invoices');
        Route::get('/invoice/{invoiceId}/items', [CustomerReturnController::class, 'getInvoiceItems'])->name('invoice.items');
        Route::get('/{return}', [CustomerReturnController::class, 'show'])->name('show');
        Route::get('/{return}/pdf', [CustomerReturnController::class, 'pdf'])->name('pdf');
        Route::delete('/{return}/cancel', [CustomerReturnController::class, 'cancel'])->name('cancel');
    });

    // Main Stock
    Route::get('/main-stock', [\App\Http\Controllers\Shared\MainStockController::class, 'index'])->name('main-stock.index');
});
