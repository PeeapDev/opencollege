<?php

namespace App\Http\Middleware;

use Closure;

class SuperAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('super_admin')) {
            abort(403, 'Access denied. Super admin privileges required.');
        }

        return $next($request);
    }
}
