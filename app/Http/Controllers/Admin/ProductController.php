<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'barcode' => 'nullable|string|max:50|unique:products',
            'description' => 'nullable|string',
            'current_price' => 'required|numeric|min:0',
            'customer_price' => 'nullable|numeric|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'تم إضافة المنتج بنجاح');
    }
}
