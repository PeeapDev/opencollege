# Two-Factor Authentication (2FA) — Specification

**Status:** Planned (larger scope, likely Q3 2026)
**DPG Criterion:** 9 — Do No Harm
**Priority:** high — single biggest protection against credential phishing

---

## 1. Why

Passwords alone are not sufficient for users who handle sensitive data
(admins, registrars, bursars). A phishing email that captures a
plaintext password gives an attacker full account access. 2FA adds a
second factor that the attacker cannot easily obtain, collapsing the
success rate of credential phishing from high to near-zero.

For OpenCollege specifically:

- Staff who modify grades should have 2FA mandatory
- Finance users who process payments should have 2FA mandatory
- Platform super-admins should have 2FA mandatory
- Students should be able to opt in

## 2. Factors supported

In rollout order:

| Factor | Implementation | Rollout |
|--------|---------------|---------|
| TOTP (Google/Microsoft Authenticator, Authy) | RFC 6238, `pragmarx/google2fa` library | Phase 1 |
| Backup codes (10 one-time codes printed after setup) | Hashed in DB, used-flag per code | Phase 1 |
| SMS OTP (via PeeapPay or Twilio) | 6-digit code, 5-min expiry | Phase 2 |
| WebAuthn / FIDO2 (hardware keys, platform biometrics) | `web-auth/webauthn-lib` | Phase 3 |
| Email OTP (fallback) | Last-resort only | Phase 2 |

SMS deserves a note: SMS OTP is weaker than TOTP due to SIM-swap
attacks, but vastly better than nothing, and for users without
smartphones it may be the only practical option.

## 3. Database schema

New migration:

```php
Schema::create('user_2fa_methods', function (Blueprint $t) {
    $t->id();
    $t->foreignId('user_id')->constrained()->cascadeOnDelete();
    $t->enum('method', ['totp', 'sms', 'email', 'webauthn']);
    $t->text('secret_encrypted');       // TOTP secret, WebAuthn credential, etc.
    $t->string('label')->nullable();    // "Phone", "Yubikey 5", etc.
    $t->timestamp('confirmed_at')->nullable();
    $t->timestamp('last_used_at')->nullable();
    $t->timestamps();
});

Schema::create('user_2fa_backup_codes', function (Blueprint $t) {
    $t->id();
    $t->foreignId('user_id')->constrained()->cascadeOnDelete();
    $t->string('code_hash');
    $t->timestamp('used_at')->nullable();
    $t->timestamps();
});

// Extend users table
Schema::table('users', function (Blueprint $t) {
    $t->boolean('two_factor_enforced')->default(false);
    $t->boolean('two_factor_bypassed_until')->nullable(); // emergency
});
```

Secrets are encrypted at rest using Laravel's built-in Crypt facade
(backed by `APP_KEY`). Backup codes are bcrypt-hashed, never stored in
cleartext.

## 4. User flow

### 4.1 First-time setup (TOTP)

1. User visits `Account → Security → Two-Factor`
2. Server generates secret → QR code
3. User scans in Authenticator app → enters current 6-digit code
4. On confirmation, secret marked `confirmed_at`, 10 backup codes
   generated and **shown once** with a "Print / Save" button
5. User can't proceed until they acknowledge seeing the backup codes

### 4.2 Login flow

1. User submits email + password as today
2. If user has confirmed 2FA → redirect to `/2fa` challenge page
3. `/2fa` accepts any confirmed method (TOTP, SMS, WebAuthn)
4. On success, session marked `twofa_verified=true`
5. Subsequent requests with the same session skip the challenge until
   session expires

### 4.3 Recovery flow

- User lost phone → uses a backup code at `/2fa`
- User lost phone AND backup codes → contacts admin
- Admin has a "Disable 2FA for user X" button that requires typing the
  user's email (confirm) + records an audit log entry

## 5. Enforcement policy

Config-driven, via `config/auth.php`:

```php
'two_factor' => [
    'required_roles' => ['super_admin', 'admin', 'bursar', 'finance_officer'],
    'required_if_feature' => ['grade_modify', 'payment_create'],
    'optional_for_students' => true,
    'grace_period_days' => 14,  // time window to set up after policy change
],
```

A `Require2FA` middleware checks the role; if 2FA is required but not
yet set up and `grace_period_days` has passed, redirects to setup.

## 6. Edge cases

- **Clock skew** — TOTP uses a 30-second window; accept the previous
  and next window to tolerate clock drift
- **Multiple devices** — allow a user to register more than one TOTP
  secret (e.g. phone + backup tablet)
- **Rate limiting** — 5 failed 2FA attempts per 10 min → same lockout
  policy as password
- **Session hijack** — `twofa_verified` flag is tied to session ID;
  regenerated on every successful 2FA challenge
- **Admin impersonation** — an admin who "logs in as" a 2FA user must
  prove their own 2FA, not the target's

## 7. What it protects against

| Threat | Mitigated? |
|--------|-----------|
| Phishing → password theft | ✅ attacker still needs the second factor |
| Password reuse from another breach | ✅ same reason |
| Credential stuffing | ✅ per-user lockout + 2FA challenge |
| Session hijacking (stolen cookies) | ⚠️ partial — see §8 |
| Malware on user's device | ❌ 2FA doesn't help |
| SIM-swap (for SMS only) | ❌ use TOTP or WebAuthn instead |

## 8. What's out of scope

- Step-up authentication on sensitive pages (considered for future)
- Device-bound session cookies (would require WebAuthn + trusted-device
  registration)
- Biometric-only (server-side cannot trust biometrics — they're an
  unlocking mechanism for the private key on the device)

## 9. Rollout plan

**Phase 1 — TOTP + backup codes (2–3 weeks)**

- Migrations
- Setup wizard
- Login challenge page
- Recovery via backup codes
- Admin disable flow
- Audit log entries for 2FA setup / use / disable / bypass

**Phase 2 — SMS fallback (1 week)**

- Integration with PeeapPay SMS gateway
- Rate limit SMS to prevent abuse (1 per minute per number)

**Phase 3 — WebAuthn (2 weeks)**

- Library integration
- Browser compatibility testing (WebAuthn needs modern browsers)

**Phase 4 — enforcement (1 week)**

- Middleware
- Grace-period logic
- Per-role config

Total: ~7–8 weeks of focused work. Could ship Phase 1 in 2 weeks if
prioritised.

## 10. References

- [NIST 800-63B](https://pages.nist.gov/800-63-3/sp800-63b.html) —
  authoritative identity assurance guidelines
- [Laravel Fortify](https://github.com/laravel/fortify) — upstream
  reference implementation for TOTP in Laravel
- [pragmarx/google2fa](https://github.com/antonioribeiro/google2fa) —
  battle-tested TOTP library

## 11. Effort estimate

Phase 1 (TOTP + backup): **~10–15 dev-days including tests**.
Full rollout through Phase 4: **~7–8 weeks**.
