# Security Policy

## Supported Versions

We actively support the following versions of OpenCollege with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

We take the security of OpenCollege seriously. If you discover a security vulnerability, please report it responsibly so we can address it quickly.

### How to Report

Send a detailed report to: **security@peeapdev.com**

Please include:

- **Description** of the vulnerability
- **Steps to reproduce** the issue
- **Potential impact** and severity assessment
- **Suggested fix** (if you have one)
- **Your contact information** for follow-up

### What to Expect

1. **Acknowledgement** -- We will acknowledge receipt of your report within 48 hours
2. **Initial Assessment** -- Within 5 business days, we'll provide an initial assessment
3. **Fix Timeline** -- We'll work with you to determine a fix timeline based on severity
4. **Credit** -- With your permission, we'll credit you in the release notes when the fix is published

### Security Best Practices

When deploying OpenCollege in production:

#### 1. Environment Configuration

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com  # Use HTTPS
```

**Never** set `APP_DEBUG=true` in production -- this exposes sensitive information.

#### 2. Change Default Credentials

After seeding the database, immediately change all default passwords:

- Super Admin: `admin@college.edu.sl` / `admin123`
- College Admins: `admin@*.college.edu.sl` / `college123`
- Sample students and staff accounts

#### 3. Protect Sensitive Files

Ensure the following files are not publicly accessible:

- `.env` -- Contains database credentials and API keys
- `storage/` -- Contains logs and uploaded files
- `bootstrap/cache/` -- Contains cached config files
- `composer.json`, `composer.lock`, `package.json` -- Exposes installed packages

Your web server should be configured to serve only from the `public/` directory.

#### 4. Database Security

- Use strong, unique database passwords
- Grant only necessary privileges to the database user
- Enable SSL/TLS for database connections if accessing remotely
- Regularly backup your database

```sql
-- Example: Create a restricted database user
CREATE USER 'opencollege'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT SELECT, INSERT, UPDATE, DELETE ON opencollege.* TO 'opencollege'@'localhost';
FLUSH PRIVILEGES;
```

#### 5. HTTPS / SSL

**Always use HTTPS in production.** Obtain a free SSL certificate from [Let's Encrypt](https://letsencrypt.org/).

```bash
# Install Certbot and obtain a certificate
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d college.edu.sl -d *.college.edu.sl
```

#### 6. File Permissions

Set appropriate file permissions to prevent unauthorized access:

```bash
# Set owner to web server user (www-data, apache, nginx, etc.)
sudo chown -R www-data:www-data /var/www/opencollege

# Set directory permissions
sudo find /var/www/opencollege -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/opencollege -type f -exec chmod 644 {} \;

# Make storage and cache writable
sudo chmod -R 775 /var/www/opencollege/storage
sudo chmod -R 775 /var/www/opencollege/bootstrap/cache
```

#### 7. Regular Updates

Keep OpenCollege and its dependencies up to date:

```bash
# Update PHP dependencies
composer update --no-dev

# Clear and rebuild caches
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 8. API Key Protection

- Store API keys (SDSL, PeeapPay) in `.env` only
- Never commit `.env` to version control
- Use different API keys for development and production
- Rotate API keys periodically

#### 9. Input Validation

OpenCollege uses Laravel's built-in validation. Ensure all custom code:

- Validates user input
- Sanitizes output to prevent XSS
- Uses prepared statements (Eloquent ORM) to prevent SQL injection
- Implements CSRF protection (enabled by default in Laravel)

#### 10. Multi-Tenant Data Isolation

OpenCollege uses the `BelongsToInstitution` trait for automatic tenant scoping. When developing custom modules:

- Always use the trait on models that store institution-specific data
- Never bypass tenant middleware
- Test data isolation thoroughly

#### 11. Logging and Monitoring

- Monitor application logs regularly: `storage/logs/laravel.log`
- Set up log rotation to prevent disk space issues
- Consider centralized logging for production (e.g., Sentry, Papertrail)
- Monitor failed login attempts

```env
# In .env, use 'daily' channel for log rotation
LOG_CHANNEL=daily
```

#### 12. Backup Strategy

Implement regular backups:

- **Database** -- Daily automated backups with off-site storage
- **Uploaded files** -- `storage/app/` directory backups
- **Environment config** -- Secure backup of `.env` file

#### 13. Session Security

Configure secure session settings in `.env`:

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true  # Requires HTTPS
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

## Known Security Considerations

### Multi-Tenancy

OpenCollege uses subdomain-based multi-tenancy with automatic data scoping. Ensure:

- Wildcard DNS is properly configured
- All models use `BelongsToInstitution` trait for automatic tenant scoping
- Custom queries include `institution_id` filters

### External Integrations

#### SDSL (NSI Verification)

- API calls to `gov.school.edu.sl` use HTTPS
- API key is stored securely in `.env`
- Responses are validated before storage

#### PeeapPay (Payments)

- Payment webhooks verify signatures before processing
- Transaction IDs are unique and validated
- Refund operations require admin approval

## Compliance

OpenCollege is designed to be compliant with:

- **GDPR** (General Data Protection Regulation) -- for EU institutions
- **Data Protection Act 2021 (Sierra Leone)** -- for local deployments
- **FERPA** (Family Educational Rights and Privacy Act) -- for US institutions

Institutions must configure data retention policies according to their local regulations.

## Security Audit

No formal third-party security audit has been conducted yet. We welcome security researchers to review the codebase and report findings responsibly.

## Hall of Fame

We recognize and thank the following security researchers (with permission):

- *No reports yet*

## Questions?

For security-related questions, contact: **security@peeapdev.com**

For general support, open a GitHub issue or discussion.

---

## Security architecture & playbooks

OpenCollege publishes detailed specs and runbooks for its safety
controls. Each document lives under `docs/security/`:

- [**Audit Log Viewer**](docs/security/audit-log-viewer.md) — design for
  the admin UI that exposes the append-only audit trail written by the
  `LogsAudit` trait.
- [**Data Breach Notification Runbook**](docs/security/breach-notification-runbook.md)
  — step-by-step playbook for institution operators to follow when a
  breach is detected or suspected. Covers containment, evidence
  preservation, regulator and data-subject notification, and
  post-incident review.
- [**Session Idle Timeout**](docs/security/session-timeout.md) — spec
  for forcing logout after configurable inactivity, with role-specific
  thresholds.
- [**Two-Factor Authentication (2FA)**](docs/security/2fa.md) — spec
  for TOTP, backup codes, SMS, and WebAuthn 2FA, with enforcement
  policy for admin and finance roles.

Currently implemented safety-net controls (commit `e7fcc58`):

- Content-Security-Policy + HSTS + X-Frame-Options via
  `SecurityHeaders` middleware
- Account lockout after 10 failed login attempts (15-min cooldown)
- Append-only audit trail on User, Institution, Grade, Invoice,
  Payment (via `LogsAudit` trait + immutable `AuditLog` model)
- Soft-delete on Institution (prevents accidental destruction)
- Forced password change on first login via `ForcePasswordChange`
  middleware
- Rate limiting: throttle:5,1 on login, 10,1 on public admission,
  5,10 on public college registration

---

**Last Updated:** April 2026
**Maintained by:** PeeapDev
