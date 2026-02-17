<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'current_price' => 'required|numeric|min:0',
            'customer_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('admin.main-stock.index')
            ->with('success', 'تم تحديث بيانات المنتج بنجاح');
    }
}
