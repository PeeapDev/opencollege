<?php

namespace App\Http\Middleware;

use Closure;
use App\Modules\Settings\Models\Institution;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TenantMiddleware
{
    public function handle($request, Closure $next)
    {
        $institution = $this->identifyTenant($request);

        if ($institution) {
            $tenantSlug = Str::slug($institution->domain ?? $institution->code, '_');

            // Tenant-specific session cookie
            config(['session.cookie' => 'oc_' . $tenantSlug . '_session']);

            // Tenant-specific session storage
            $sessionPath = storage_path('framework/sessions/tenant_' . $institution->id);
            if (!is_dir($sessionPath)) {
                mkdir($sessionPath, 0775, true);
            }
            config(['session.files' => $sessionPath]);
            config(['session.domain' => null]);

            // Bind tenant to container
            app()->instance('institution', $institution);
            app()->instance('institution_id', $institution->id);

            // Force URL root for subdomains
            if ($institution->id != 1) {
                \URL::forceRootUrl($request->getSchemeAndHttpHost());
            }

            // Handle tenant switching
            $this->handleTenantSwitch($institution);
        }

        return $next($request);
    }

    protected function identifyTenant($request): ?Institution
    {
        $host = $request->getHttpHost();
        $shortUrl = preg_replace('#^https?://#', '', rtrim(env('APP_URL', 'http://localhost'), '/'));

        // Extract subdomain
        $domain = str_replace('.' . $shortUrl, '', $host);

        if ($domain === $host) {
            $domain = null;
        }

        $reserved = ['api', 'admin', 'www', 'mail', 'ftp', 'cpanel', 'webmail', 'emis'];

        // Subdomain lookup
        if ($domain && !in_array(strtolower($domain), $reserved)) {
            $institution = Institution::whereRaw('LOWER(domain) = ?', [strtolower($domain)])
                ->where('active', true)
                ->first();

            if ($institution) {
                return $institution;
            }

            // Check inactive
            $inactive = Institution::whereRaw('LOWER(domain) = ?', [strtolower($domain)])->first();
            if ($inactive) {
                abort(403, 'This college is currently inactive. Contact the administrator.');
            }

            abort(404, 'College not found. Please check the subdomain.');
        }

        // Main domain = super admin (institution ID 1)
        if ($host === $shortUrl || !$domain) {
            return Institution::where('id', 1)->first();
        }

        // Custom domain support
        $institution = Institution::whereRaw('LOWER(custom_domain) = ?', [strtolower($host)])
            ->where('active', true)
            ->first();

        return $institution;
    }

    protected function handleTenantSwitch(Institution $current): void
    {
        $sessionTenantId = Session::get('current_tenant_id');

        if ($sessionTenantId && $sessionTenantId !== $current->id) {
            if (Auth::check()) {
                Auth::logout();
            }
            Session::flush();
            Session::regenerate(true);
        }

        Session::put('current_tenant_id', $current->id);
    }
}
