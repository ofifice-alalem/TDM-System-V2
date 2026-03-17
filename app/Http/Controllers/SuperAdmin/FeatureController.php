<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::orderBy('role')->orderBy('label')->get();
        return view('super-admin.features.index', compact('features'));
    }

    public function update(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'is_enabled' => 'required|boolean',
            'mode'       => 'required|in:permanent,scheduled_off,scheduled_on',
            'starts_at'  => 'nullable|date',
            'ends_at'    => 'nullable|date|after_or_equal:starts_at',
        ]);

        // في وضع permanent لا نحتاج تواريخ
        if ($validated['mode'] === 'permanent') {
            $validated['starts_at'] = null;
            $validated['ends_at']   = null;
        }

        $feature->update($validated);

        return response()->json([
            'success'          => true,
            'is_active_now'    => $feature->isCurrentlyEnabled(),
        ]);
    }
}
