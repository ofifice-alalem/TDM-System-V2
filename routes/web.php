<?php

use Illuminate\Support\Facades\Route;
use App\Models\InvoiceDiscountTier;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $roleId = auth()->user()->role_id;
    
    return match($roleId) {
        3 => redirect()->route('marketer.stock.index'),
        2 => redirect()->route('warehouse.requests.index'),
        1 => redirect()->route('admin.main-stock.index'),
        default => abort(403, 'دور غير معروف'),
    };
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', function() { return view('profile.edit'); })->name('profile.edit');
    Route::patch('/profile', function() { return back(); })->name('profile.update');
    Route::delete('/profile', function() { return back(); })->name('profile.destroy');
});

Route::get('/calculate-invoice-discount', function () {
    $amount = request('amount', 0);
    
    $tier = InvoiceDiscountTier::where('is_active', true)
        ->where('min_amount', '<=', $amount)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->orderBy('min_amount', 'desc')
        ->first();
    
    if (!$tier) {
        return response()->json([
            'discount_amount' => 0,
            'discount_type' => null,
            'discount_value' => null
        ]);
    }
    
    $discountAmount = 0;
    if ($tier->discount_type === 'percentage') {
        $discountAmount = $amount * ($tier->discount_percentage / 100);
    } else {
        $discountAmount = $tier->discount_amount;
    }
    
    return response()->json([
        'discount_amount' => round($discountAmount, 2),
        'discount_type' => $tier->discount_type,
        'discount_value' => $tier->discount_type === 'percentage' ? $tier->discount_percentage : $tier->discount_amount
    ]);
});

require __DIR__.'/auth.php';
require __DIR__.'/marketer.php';
require __DIR__.'/warehouse.php';
require __DIR__.'/admin.php';
