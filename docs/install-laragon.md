# Installing OpenCollege on Windows with Laragon

A complete, step-by-step walkthrough using the **web setup wizard**.
No `.env` editing, no CLI migration. If you can follow 11 numbered
steps, you'll have OpenCollege running in ~15 minutes.

**What you'll end up with:**
- A working OpenCollege install at `http://opencollege.test`
- HEMIS government portal at the root URL
- A super-admin account with credentials you chose yourself
- (Optional) one demo college seeded with sample data
- Ability to register real colleges via the platform admin UI

---

## Before you start

- [ ] **Windows 10 or 11**
- [ ] **Laragon Full edition** from https://laragon.org (the Full
  installer — bundles PHP, MySQL, Node, Composer, Git, HeidiSQL)
- [ ] **~2 GB free disk space**
- [ ] **Internet connection** for the first `composer install`

---

## Step 1 — Start Laragon

1. Open Laragon
2. Click **Start All** — wait for Apache and MySQL to turn green
3. If port 80 is in use, stop the conflicting program (often IIS or
   Skype) and click Start All again

---

## Step 2 — Open Laragon's terminal

Right-click anywhere inside the Laragon window → **Terminal**.

This opens a shell with PHP, MySQL, Composer, and Git already on your
PATH. Verify:

```
php -v
composer --version
mysql --version
```

All three should print version numbers.

> **Note:** `mysql` only resolves inside the Laragon terminal. Plain
> PowerShell won't find it unless you add
> `C:\laragon\bin\mysql\mysql-*\bin` to your global PATH.

---

## Step 3 — Clone the repository

```
cd C:/laragon/www
git clone https://github.com/PeeapDev/opencollege.git
cd opencollege
```

Laragon auto-creates a vhost for any folder in `C:/laragon/www` — the
folder name becomes the hostname (`opencollege` → `opencollege.test`).

---

## Step 4 — Install dependencies

```
composer install
```

Takes 2–5 minutes on first run (downloads ~200 MB of packages). When
it finishes, you'll see `Generating optimized autoload files`.

If you hit `ext-intl is missing`: Laragon → **PHP → Extensions** →
tick `intl`, restart Apache, re-run.

---

## Step 5 — Create the empty database

> These are **HeidiSQL clicks**, not terminal commands.

1. Laragon window → right-click → **Database → HeidiSQL**
2. In HeidiSQL left panel, double-click **"localhost"** to connect
   (no password by default)
3. Right-click the localhost root → **Create new → Database**
4. Name: **`opencollege`**, Collation: **`utf8mb4_unicode_ci`**
5. Click **OK**

You've created an empty DB. The wizard fills it with tables in Step 8.

---

## Step 6 — Visit the setup wizard

In your browser, go to:

```
http://opencollege.test
```

First visit auto-redirects to `/install`. You'll see the HEMIS install
wizard welcome screen.

> **No need to run `php artisan serve`** — Laragon's Apache already
> serves `opencollege.test` because your code is under
> `C:/laragon/www/`.
>
> **If you do want to use `artisan serve`:** note that port 8000 is
> blocked on many Windows machines (Hyper-V reserves it). If you see
> `An attempt was made to access a socket in a way forbidden by its
> access permissions`, use `php artisan serve --port=8080` instead.
>
> **"Site can't be reached":** start Laragon (Step 1); check
> `C:\Windows\System32\drivers\etc\hosts` has
> `127.0.0.1 opencollege.test` (Laragon adds this automatically).

---

## Step 7 — Welcome + Requirements

**Welcome** → Click **Begin →**

**Requirements** → every row should show green `✓ OK`. If any are red,
install the missing extension (Laragon Full bundles all of them — this
should just work).

---

## Step 8 — Enter database credentials

Enter exactly:

- **Host:** `127.0.0.1`
- **Port:** `3306`
- **Database:** `opencollege` *(what you created in Step 5)*
- **Username:** `root`
- **Password:** *leave empty* (Laragon's default)

Click **Test & Continue →**. The wizard tests the connection live and
refuses to advance if it fails.

---

## Step 9 — Enter site info

- **Application name:** e.g. "OpenCollege Local" or your school name
- **Application URL:** `http://opencollege.test`
- **Timezone:** your local (e.g. `UTC`, `Europe/London`, `Asia/Kolkata`, `Africa/Lagos`)
- **Mail from:** anything like `admin@opencollege.test` (locally the
  `log` driver is used — emails go to `storage/logs/laravel.log`)

---

## Step 10 — Create your super-admin account

- **Full name:** your name
- **Email:** any valid email
- **Password:** at least 8 chars, letters + numbers
- **Confirm password:** same
- ☑ **Seed with demo data** — tick this on first install to get a
  sample college to explore

**There are no default passwords.** Whatever you type here becomes
your login.

---

## Step 11 — Finalize

Click the big blue button. You'll see a spinner while the wizard:

- Writes `.env` with your DB + site settings
- Generates `APP_KEY`
- Runs all migrations
- Seeds demo data (if ticked)
- Creates your super-admin user
- Writes `storage/installed` lock file

~30–60 seconds. When done, you're redirected to the Done screen with a
"Go to login" button → go to `http://opencollege.test/login` and sign
in with the email + password you chose in Step 10.

---

## You're done

| URL | What |
|-----|------|
| `http://opencollege.test` | HEMIS government dashboard |
| `http://opencollege.test/login` | Platform admin login |
| `http://opencollege.test/superadmin/colleges` | Manage tenant colleges |
| `http://csl.opencollege.test` | Demo college (if you ticked seed) |

---

## Visiting the demo college

If you seeded demo data in Step 10, the demo college is at
`csl.opencollege.test`. To access it on Windows:

**Option A — add one hosts entry:**
1. Notepad as Administrator
2. Open `C:\Windows\System32\drivers\etc\hosts` (filter: All files)
3. Add: `127.0.0.1 csl.opencollege.test`
4. Save

**Option B — Laragon wildcard (better for multiple colleges):**
1. Laragon → **Preferences → Services & ports** → tick
   **Acrylic DNS Proxy** → Apply
2. Open `C:\Program Files (x86)\Acrylic DNS Proxy\AcrylicHosts.txt`
   as Admin
3. Add: `127.0.0.1 *.opencollege.test`
4. Restart Acrylic via Laragon preferences

Then any subdomain you create (via **Platform → Manage Colleges** on
the HEMIS sidebar) resolves automatically.

---

## Stopping / restarting later

**Stop:** Laragon → Stop All. Vhost config persists.

**Start again tomorrow:** Laragon → Start All → open
`http://opencollege.test`. Everything comes back exactly as you left it.

**Pull newer code:**
```
cd C:/laragon/www/opencollege
git pull
composer install
php artisan migrate --force
php artisan config:clear
```

The wizard will NOT re-run — it's locked by `storage/installed`. If
you *want* to re-run it (e.g. to reconfigure DB), delete both `.env`
and `storage/installed` first.

---

## Troubleshooting

| Error | Cause | Fix |
|-------|-------|-----|
| `opencollege.test` won't resolve | Laragon not running / hosts entry missing | Start Laragon; check `C:\Windows\System32\drivers\etc\hosts` |
| "Please provide a valid cache path" (500 on first visit) | `storage/framework/cache/data` or `views` missing on a fresh clone | The repo now ships `.gitkeep` files — if you still see this, `mkdir -p storage/framework/cache/data storage/framework/views storage/logs` |
| `artisan serve` fails: "socket forbidden by access permissions" | Windows Hyper-V reserves port 8000 | `php artisan serve --port=8080` — or just use Laragon's Apache vhost |
| "Could not connect" in wizard Step 8 | Wrong creds or MySQL stopped | Laragon → Start All; verify `root` / empty password in HeidiSQL |
| Wizard loops back to `/install` after CLI migrate | Lock file not created | Either re-run through wizard (delete `.env` first), or `touch storage/installed` |
| 419 Page Expired on login | Stale CSRF cookie from before install | Clear cookies for `opencollege.test`, or use incognito |
| "Class X not found" after `git pull` | New composer deps | `composer install` |
| Duplicate column errors in wizard step 11 | DB already had tables from a previous install | In HeidiSQL: right-click `opencollege` database → Drop → recreate empty → re-run wizard |

For any other issue, check `storage/logs/laravel.log` for the full
stack trace.

---

## Next steps

- [`docs/database.md`](database.md) — full schema and seeder contents
- [`INSTALLATION.md`](../INSTALLATION.md) — wildcard DNS + SSL for
  production
- [`docs/security/`](security/) — security architecture before
  deploying beyond localhost
