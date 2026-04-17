<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only allow HEMIS routes when the request is on the root domain (institution id=1).
 * On tenant subdomains (njala, unimak, ...), these routes 404.
 */
class HemisOnRootDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $institution = app()->bound('institution') ? app('institution') : null;
        if (!$institution || $institution->id != 1) {
            abort(404);
        }

        return $next($request);
    }
}
