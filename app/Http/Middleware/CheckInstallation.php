<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * If the app is NOT installed, force redirect to /install for any
 * non-install route. If it IS installed, block access to /install routes.
 */
class CheckInstallation
{
    public function handle(Request $request, Closure $next): Response
    {
        $installed = file_exists(storage_path('installed'));
        $isInstallRoute = $request->is('install') || $request->is('install/*');

        if (!$installed && !$isInstallRoute) {
            return redirect('/install');
        }

        if ($installed && $isInstallRoute) {
            abort(404);
        }

        return $next($request);
    }
}
