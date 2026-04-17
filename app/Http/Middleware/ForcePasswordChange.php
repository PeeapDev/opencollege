<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force first-login password change — DPG Criterion 9.
 * Users with `must_change_password=true` are redirected to the change form.
 */
class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || empty($user->must_change_password)) {
            return $next($request);
        }

        // Allow the password-change endpoints and logout
        $allowed = [
            'password/force-change',
            'password/force-update',
            'logout',
        ];

        foreach ($allowed as $path) {
            if ($request->is($path) || $request->is($path.'/*')) {
                return $next($request);
            }
        }

        return redirect('/password/force-change')
            ->with('warning', 'You must change your temporary password before continuing.');
    }
}
