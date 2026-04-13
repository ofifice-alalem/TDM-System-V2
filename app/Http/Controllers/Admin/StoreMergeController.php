<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\Admin\StoreMergeService;
use Illuminate\Http\Request;

class StoreMergeController extends Controller
{
    public function __construct(private StoreMergeService $service) {}

    public function index()
    {
        $stores = Store::orderBy('name')->get(['id', 'name', 'phone']);
        return view('admin.store-merge.index', compact('stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'primary_id'   => 'required|exists:stores,id',
            'duplicate_id' => 'required|exists:stores,id|different:primary_id',
        ]);

        try {
            $this->service->merge($request->primary_id, $request->duplicate_id);
            return back()->with('success', 'تم دمج المتجرين بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
