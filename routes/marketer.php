<?php

use App\Http\Controllers\Marketer\MarketerRequestController;
use Illuminate\Support\Facades\Route;

// Temporary: Simulate logged-in marketer (ID=3)
Route::middleware(['web'])->group(function () {
    Route::prefix('marketer')->name('marketer.')->group(function () {
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [MarketerRequestController::class, 'index'])->name('index');
            Route::get('/create', [MarketerRequestController::class, 'create'])->name('create');
            Route::post('/', [MarketerRequestController::class, 'store'])->name('store');
            Route::get('/{request}', [MarketerRequestController::class, 'show'])->name('show');
            Route::get('/{request}/pdf', [MarketerRequestController::class, 'pdf'])->name('pdf');
            Route::patch('/{request}/cancel', [MarketerRequestController::class, 'cancel'])->name('cancel');
        });
    });
});
