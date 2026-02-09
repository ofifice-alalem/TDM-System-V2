<?php

use Illuminate\Support\Facades\Route;
use App\Models\InvoiceDiscountTier;

Route::get('/', function () {
    return view('welcome');
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

require __DIR__.'/marketer.php';
require __DIR__.'/warehouse.php';
require __DIR__.'/admin.php';
