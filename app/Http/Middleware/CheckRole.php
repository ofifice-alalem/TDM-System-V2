<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRoleId = auth()->user()->role_id;

        $allowedRoleId = match($role) {
            'marketer' => 3,
            'warehouse' => 2,
            'admin' => 1,
            default => null,
        };

        if ($userRoleId !== $allowedRoleId) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
