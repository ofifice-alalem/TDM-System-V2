<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\FeatureController;

Route::middleware(['web', 'auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
        Route::patch('/features/{feature}', [FeatureController::class, 'update'])->name('features.update');
    });
