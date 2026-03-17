<?php

namespace App\Http\Middleware;

use App\Models\Feature;
use Closure;
use Illuminate\Http\Request;

class CheckFeature
{
    public function handle(Request $request, Closure $next, string $featureKey)
    {
        $feature = Feature::where('key', $featureKey)->first();

        if ($feature && !$feature->isCurrentlyEnabled()) {
            return redirect()->route('feature.disabled');
        }

        return $next($request);
    }
}
