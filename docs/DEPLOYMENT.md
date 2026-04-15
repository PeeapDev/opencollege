# Deployment Guide

This document covers **every way** to get OpenCollege running, from a
curious developer's laptop to a production VPS, and how the project's
CI/CD pipeline automatically ships `main` to the live server.

---

## 1. The web installer (easiest — recommended)

OpenCollege ships with a **browser-based setup wizard** modelled on the
CodeCanyon pattern. You do not need to touch `.env`, run migrations by
hand, or create the first admin user from tinker. Everything happens
through 6 web screens.

### What you need beforehand

1. A server / local environment with PHP 8.2+ and the required extensions
   (see `INSTALLATION.md` Section 1)
2. Composer installed
3. An **empty** MySQL or MariaDB database created
4. The code deployed to your web server (git clone + `composer install`)

### The 6-step flow

| # | Step | What happens |
|---|------|--------------|
| 1 | **Welcome** | Brief intro, list of prerequisites |
| 2 | **Requirements** | Live check of PHP version, all required extensions, `storage/` & `bootstrap/cache` writability |
| 3 | **Database** | Enter host / port / name / user / password — installer tests the connection live |
| 4 | **Site** | App name, URL, timezone, mail-from address |
| 5 | **Admin** | Create the first super-admin account + optional demo data seed |
| 6 | **Finalize** | One-click: writes `.env`, runs `key:generate`, `migrate`, creates the admin user, writes a lock file |

### How to trigger it

After deploying the code, simply visit:

```
https://your-domain.example/install
```

If installation is incomplete, every other URL on the site automatically
redirects to `/install`, so users can't accidentally hit a broken app.

### What prevents re-installs

Once step 6 finishes, a lock file is written to `storage/installed`.
The `CheckInstallation` middleware then:

- 404s any request to `/install/*` (protecting against malicious
  re-install attempts)
- Allows all other routes through normally

To **re-run the installer** (for example on a dev machine being
reinitialised), delete the lock file:

```bash
rm storage/installed
# and optionally:
rm .env
```

Then visit `/install` again.

---

## 2. Manual installation (CLI)

If you'd rather install from the command line, see `INSTALLATION.md`
for the traditional Laravel setup path. Use the web installer for
single-site deployments; use the CLI when scripting multi-tenant rollouts.

---

## 3. Local development environments

`INSTALLATION.md` Appendix A covers all the local paths:

- **Laragon** (Windows, easiest)
- **XAMPP** (Windows/macOS/Linux, familiar)
- **Herd** (macOS/Windows, Laravel-tuned)
- **Docker Compose / Laravel Sail** (any OS)

Each one supports the web installer at `/install` after `composer install`.

---

## 4. CI/CD — automatic deployment to production

### The pipeline

OpenCollege uses a single GitHub Actions workflow at
`.github/workflows/deploy.yml` that fires on every push to `main` and can
also be triggered manually from the Actions tab.

On every commit to `main`, the workflow:

1. **Checks out** the repository
2. **Sets up PHP 8.3** with all required extensions
3. **Runs `composer install --no-dev --optimize-autoloader`**
4. **Builds frontend assets** (`npm ci && npm run build`)
5. **Rsyncs** the Laravel source to the production server via SSH,
   using a strict allowlist (never touches `.env`, `storage/app/*`, or
   sibling domains hosted on the same account)
6. **Runs `artisan migrate --force`** and rebuilds config/route/view caches
7. **Brings the app back up** (it's put into maintenance mode briefly during
   the migration window)

### Required GitHub secrets

Set these under **Settings → Secrets and variables → Actions**:

| Secret | Example | Purpose |
|--------|---------|---------|
| `DEPLOY_SSH_KEY` | `-----BEGIN OPENSSH PRIVATE KEY-----…` | Private key for passwordless SSH |
| `DEPLOY_HOST` | `college.edu.sl` | Production hostname |
| `DEPLOY_USER` | `collegeedu` | SSH user on the host |
| `DEPLOY_PATH` | `/home/collegeedu/public_html` | Laravel document root parent on the host |
| `DEPLOY_PORT` | `22` | SSH port |

### Setting up the deploy key

On your production server (as the SSH user that the workflow will
connect as):

```bash
mkdir -p ~/.ssh && chmod 700 ~/.ssh
# Generate a dedicated deploy key PAIR on a trusted workstation, not the server
# Then install only the PUBLIC half:
echo "ssh-ed25519 AAAA…your-public-key…" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

Store the **private half** as the `DEPLOY_SSH_KEY` GitHub secret.

### What gets deployed

The rsync command uses a strict include-list:

```
/app  /bootstrap  /config  /database  /docs  /public  /resources
/routes  /tests  /vendor  /artisan  /composer.*  /package.*
/README.md  /LICENSE  /AUTHORS.md  /PRIVACY.md  /INSTALLATION.md
/CONTRIBUTING.md  /SECURITY.md  /.editorconfig  /.env.example
```

The following are **never** touched on the live server:

- `.env`  — production credentials
- `storage/app/public/*`  — user uploads
- `storage/logs/*`  — runtime logs
- `bootstrap/cache/*`  — regenerated post-deploy
- Any sibling directory (addon domains, WordPress installs, etc.)

### Rollback

If a bad deploy ships, roll back by reverting the commit on `main`:

```bash
git revert <bad-commit-sha>
git push origin main
```

The workflow will redeploy the reverted state within 2–3 minutes.

For an **instant** rollback without waiting for CI, SSH to the server
and restore from the nightly backup — see Section 5.

### Optional: require approval before production deploys

You can put the pipeline behind a manual approval gate:

1. Go to **Settings → Environments**
2. Click **production** (the workflow already references it)
3. Check **Required reviewers** and add maintainers
4. Save

From then on, every push to `main` builds, then pauses in the Actions
tab waiting for a click before touching the live server.

---

## 5. Backups

The deployment pipeline does **not** take backups automatically — that
is the operator's responsibility. Recommended: schedule a nightly
mysqldump on the server and copy the result off-site (S3, Backblaze,
another cPanel account, etc.):

```cron
15 2 * * * /usr/bin/mysqldump -u backupuser -p'$ECRET' opencollege | gzip > /home/backups/opencollege-$(date +\%Y\%m\%d).sql.gz
```

---

## 6. End-to-end test checklist

Use this when evaluating a fresh install:

- [ ] Clone repo → `composer install` → visit `/install`
- [ ] Step 2 — all requirements green
- [ ] Step 3 — DB connection test passes
- [ ] Step 6 — installer finishes, lock file written
- [ ] Root URL no longer redirects to `/install`
- [ ] Log in with the super-admin you just created
- [ ] Visit `/api/export/students?format=json` — returns valid JSON
- [ ] Visit `/api/me/export` — returns a JSON bundle
- [ ] Try logging in with wrong password 6 times — rate limit kicks in
- [ ] Push any commit to `main` — CI deploys to staging within 3 min

If all boxes check, the installation is DPG-grade.
