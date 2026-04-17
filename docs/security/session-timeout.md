# Session Timeout for Inactive Users — Specification

**Status:** Planned
**DPG Criterion:** 9 — Do No Harm
**Related:** `config/session.php`, `app/Http/Middleware/ForcePasswordChange.php`

---

## 1. Problem

Without an idle timeout, a logged-in user who walks away from their
computer leaves the session exposed. For a registrar reviewing grades
or a bursar handling payments, this is a direct harm vector — anyone
with physical access to the machine can act as them.

Laravel's built-in `SESSION_LIFETIME` is an **absolute** lifetime
(default 120 minutes), not an **idle** timeout. A session that is
actively used for 2 hours is automatically extended, but a session that
sits idle for 60 minutes and is then picked up by a different person
is still valid.

## 2. Goals

1. Log out users who have been idle for more than a configurable number
   of minutes (default: 30).
2. Warn users shortly before the timeout so they can keep working if
   active.
3. Different roles can have different timeouts (admin stricter than
   student).
4. Graceful redirect to login with a "you were signed out due to
   inactivity" message.
5. No impact on **absolute** session lifetime — this sits on top of it.

## 3. Configuration

`config/session.php` additions:

```php
'idle_timeout' => [
    'enabled'   => env('SESSION_IDLE_ENABLED', true),
    'default'   => env('SESSION_IDLE_MINUTES', 30),
    'warn_at'   => env('SESSION_IDLE_WARN_MINUTES', 25),
    'per_role'  => [
        'super_admin' => 15,
        'admin'       => 20,
        'bursar'      => 20,
        'staff'       => 30,
        'student'     => 60,
    ],
],
```

## 4. Implementation

### 4.1 Middleware — `EnforceIdleTimeout`

```php
class EnforceIdleTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !config('session.idle_timeout.enabled')) {
            return $next($request);
        }

        $user  = Auth::user();
        $role  = $user->primaryRoleName() ?? 'default';
        $mins  = config("session.idle_timeout.per_role.$role")
              ?? config('session.idle_timeout.default');

        $last = $request->session()->get('last_activity_at');
        $now  = now();

        if ($last && $now->diffInMinutes($last) >= $mins) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')
                ->with('warning', "Signed out due to {$mins} minutes of inactivity.");
        }

        $request->session()->put('last_activity_at', $now);
        return $next($request);
    }
}
```

Registered in `bootstrap/app.php` alongside `SecurityHeaders` and
`ForcePasswordChange`. Placed after `auth` so only runs for logged-in
users.

### 4.2 Client-side warning banner

A small JS snippet injected into the admin layout:

```javascript
// Pings a harmless endpoint every 60s to read remaining seconds.
// 5 minutes before timeout, show a modal: "Still working? [Stay signed in] [Log out]"
// Clicking "Stay signed in" makes any request (e.g. POST /session/ping) which
// refreshes last_activity_at via the middleware.
```

Endpoint: `POST /session/ping` — returns `{ remaining: 1234 }` seconds.

### 4.3 Exemptions

- Login page: no timeout (not logged in yet)
- Public frontend: no timeout (no session context)
- File uploads in progress: bump `last_activity_at` every 15 seconds
  client-side during the upload

## 5. Interaction with remember-me

If a user checked "Remember me" at login, we honour that: when the idle
timeout fires, we log them out of the current session but leave the
`remember_token` cookie so they're auto-logged-in on next visit. We
still require them to enter 2FA (once that ships) since idle logout is
treated as a new session.

## 6. Persistence across server restart

The `last_activity_at` lives in the session, which lives in the
configured session store (database or Redis on production). Server
restarts don't clobber idle state.

## 7. Rollout plan

1. Ship middleware + config behind `SESSION_IDLE_ENABLED=false` default
2. Enable on staging for a week with verbose logging to catch false
   positives (e.g. long-running exports)
3. Enable for `super_admin` and `admin` roles on production
4. Enable for all roles

## 8. Testing

Tests to write:

- `IdleTimeoutTest::idle_user_is_logged_out_after_threshold`
- `IdleTimeoutTest::active_user_is_not_logged_out`
- `IdleTimeoutTest::role_specific_threshold_is_honoured`
- `IdleTimeoutTest::ping_endpoint_refreshes_activity`
- `IdleTimeoutTest::remember_me_cookie_persists_after_idle_logout`

## 9. Effort estimate

~1 dev-day including tests and UI banner.
