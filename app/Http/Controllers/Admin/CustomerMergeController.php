<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\Admin\CustomerMergeService;
use Illuminate\Http\Request;

class CustomerMergeController extends Controller
{
    public function __construct(private CustomerMergeService $service) {}

    public function index()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name', 'phone']);
        return view('admin.customer-merge.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'primary_id'   => 'required|exists:customers,id',
            'duplicate_id' => 'required|exists:customers,id|different:primary_id',
        ]);

        try {
            $this->service->merge($request->primary_id, $request->duplicate_id);
            return back()->with('success', 'تم دمج العميلين بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
