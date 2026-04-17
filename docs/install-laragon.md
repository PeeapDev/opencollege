# Installing OpenCollege on Windows with Laragon

A complete, step-by-step walkthrough. No prior PHP/Laravel knowledge
assumed. If you can follow 15 numbered steps, you'll have OpenCollege
running on your laptop at http://opencollege.test.

**What you'll end up with:**
- A working OpenCollege install at http://opencollege.test
- Sample data: 1 demo college, ~20 students, faculties, programs
- HEMIS government portal at the root URL (where you log in as platform
  super-admin)
- Ability to create real colleges as subdomains like
  http://njala.opencollege.test

**Estimated time:** 15–20 minutes.

---

## Before you start

Make sure you have:

- [ ] **Windows 10 or 11**
- [ ] **Laragon Full edition** installed from https://laragon.org
  (download the big installer — the "Full" one, not "Lite"; it bundles
  PHP, MySQL, Node, Composer, Git, HeidiSQL)
- [ ] **At least 2 GB of free disk space**
- [ ] **An internet connection** (composer will download ~200 MB of
  PHP packages on first run)

---

## Step 1 — Start Laragon

1. Open Laragon (double-click the desktop icon, or find it in the Start menu)
2. Click **Start All** (big button, bottom-right)
3. Wait until the panel shows:
   - ✅ Apache: Running on port 80
   - ✅ MySQL: Running on port 3306
4. If Apache shows a port-in-use error, another program is using port 80.
   Stop it (usually IIS, Skype, or another local web server) and click
   Start All again.

---

## Step 2 — Verify Laragon has what we need

Open a Laragon terminal — right-click anywhere inside the Laragon window
→ **Terminal**. This opens a command prompt with PHP, MySQL, and
Composer already on the PATH.

In the terminal, verify each tool:

```
php -v
composer --version
node -v
mysql --version
```

Each should print a version number. If any says "command not found",
your Laragon install is incomplete — reinstall the Full edition.

Required minimum versions:
- PHP **8.2+**
- Composer **2.5+**
- Node **18+**
- MySQL **8.0+** (Laragon ships 8.x, you're fine)

---

## Step 3 — Clone the repository

Still in the Laragon terminal:

```
cd C:/laragon/www
git clone https://github.com/PeeapDev/opencollege.git
cd opencollege
```

Laragon automatically creates a vhost for any folder in `C:/laragon/www`.
The folder name becomes the hostname, so `opencollege` → `opencollege.test`.

---

## Step 4 — Install PHP dependencies

```
composer install
```

Composer reads `composer.lock` and downloads ~111 packages. First run
takes 2–5 minutes. You'll see lots of "Downloading ..." and
"Installing ..." lines. Wait for it to print
`Generating optimized autoload files` and return to the prompt.

If you see an error like `ext-intl is missing`: in Laragon → **PHP →
Extensions** → tick `intl`, then restart Apache.

---

## Step 5 — Create environment file

```
copy .env.example .env
php artisan key:generate
```

The first command copies the template. The second fills in a random
`APP_KEY` used to encrypt session cookies.

---

## Step 6 — Create the database (this is the step most people miss)

> **Important:** the next few lines are NOT terminal commands. They go
> inside a MySQL tool called HeidiSQL. Do not paste them into
> PowerShell — that's the #1 beginner mistake.

1. Back on the Laragon main window, right-click → **Database → HeidiSQL**
2. HeidiSQL opens. In the left panel you'll see **"localhost"** under
   "Session manager" — double-click it to connect (no password needed).
3. In the left panel (after connection), **right-click the localhost root**
   → **Create new → Database**
4. In the dialog:
   - **Name:** `opencollege`
   - **Collation:** choose `utf8mb4_unicode_ci`
5. Click **OK**

You've just created an empty database. HeidiSQL now shows `opencollege`
in the tree. Leave HeidiSQL open — we'll come back to it at the end to
check the tables.

---

## Step 7 — Point `.env` at the database

Open the `.env` file you created in Step 5. In the Laragon terminal:

```
notepad .env
```

(Or use VS Code / any editor.)

Find and change these lines to match Laragon's defaults:

```dotenv
APP_URL=http://opencollege.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=opencollege
DB_USERNAME=root
DB_PASSWORD=
```

Save and close.

Why `root` with empty password? That's what Laragon ships by default.
In production you'd use a dedicated user, but for local development
this is fine.

---

## Step 8 — Run migrations + seed sample data

Back in the Laragon terminal:

```
php artisan migrate --seed
```

You'll see ~40 migration lines scrolling past (creating tables like
users, institutions, faculties, departments, programs, students…),
then the seeder populates a demo college with sample data.

Expected output ends with:
```
INFO  Database seeded successfully.
```

If it fails with "Base table or view not found" — you forgot Step 6.
If it fails with "Access denied for user 'root'" — check your `.env`
password in Step 7.

---

## Step 9 — Generate the storage symlink

```
php artisan storage:link
```

This lets uploaded files (student photos, logos) be served by the web
server.

---

## Step 10 — Restart Laragon so it picks up the new vhost

Click **Stop All** then **Start All** in Laragon. This isn't strictly
needed (Laragon usually auto-detects new folders) but it guarantees
`opencollege.test` resolves on the next step.

---

## Step 11 — Visit the site

Open your browser and go to:

```
http://opencollege.test
```

You should see the **HEMIS government portal dashboard** — that's the
root-level view with stat cards showing institutions, students,
programs, and a recent institutions table.

If the browser says "site can't be reached":
- Restart Laragon once more (Step 10)
- Check Windows hosts file: `C:\Windows\System32\drivers\etc\hosts`
  should have a line added by Laragon:
  `127.0.0.1 opencollege.test` — if it's missing, add it manually
  (you'll need admin privileges).

---

## Step 12 — Log in

Click **Sign In** (top right), or go directly to
http://opencollege.test/login.

The seeder created a platform admin account:

- **Email:** `admin@opencollege.test`
- **Password:** `password`

> ⚠️ Change this immediately if you ever deploy beyond localhost. This
> is for local development only.

After logging in, you're back on the HEMIS dashboard with the admin
sidebar visible.

---

## Step 13 — Try the demo college

The seeder also created a demo college called "College of Sierra Leone"
with subdomain slug `csl`. To visit it you need to add it to your hosts
file:

1. Open Notepad **as Administrator** (right-click → Run as administrator)
2. File → Open → paste `C:\Windows\System32\drivers\etc\hosts`
   (change the file filter to "All files" to see it)
3. Add this line at the bottom:
   ```
   127.0.0.1 csl.opencollege.test
   ```
4. Save
5. Open http://csl.opencollege.test in your browser

You should see the college's own portal. Log in with:
- **Email:** `admin@csl.opencollege.test`
- **Password:** `password`

---

## Step 14 — (Optional) Wildcard local subdomains

Adding every college to the hosts file is tedious if you're creating
lots of tenants locally. Laragon can auto-resolve any `*.opencollege.test`
via Acrylic DNS:

1. Laragon menu → **Preferences → Services & ports**
2. Check **Acrylic DNS Proxy**
3. Click **Apply**
4. Edit `C:\Program Files (x86)\Acrylic DNS Proxy\AcrylicHosts.txt`
   (again, Notepad as Administrator):
   ```
   127.0.0.1 *.opencollege.test
   ```
5. Restart Acrylic DNS from Laragon's preferences

Now any subdomain — `njala.opencollege.test`, `fbc.opencollege.test`,
whatever — resolves automatically.

---

## Step 15 — Create a real college (optional)

To create a real college tenant instead of using the demo one:

1. On http://opencollege.test, log in as platform admin
2. Go to **Platform → Manage Colleges** in the sidebar
3. Click **Register new college**
4. Fill in:
   - Name: University of Makeni
   - Code: UNIMAK
   - Domain: `unimak` (this becomes the subdomain)
   - Type: University
   - Admin email: `admin@unimak.opencollege.test`
5. Submit. The page shows a **one-time temporary password** for the
   college's admin — copy it.
6. Visit http://unimak.opencollege.test and log in with those
   credentials.

---

## You're done

What you have now:

- `http://opencollege.test` — HEMIS government dashboard
- `http://opencollege.test/login` — platform admin login
- `http://opencollege.test/superadmin/colleges` — manage tenant colleges
- `http://csl.opencollege.test` — demo college "College of Sierra Leone"
- `http://<slug>.opencollege.test` — any college you create

---

## Stopping and restarting later

**To stop:**
- Laragon window → Stop All
- Close the Laragon window (the vhost stays configured)

**To restart tomorrow:**
- Open Laragon → Start All
- Open `http://opencollege.test` — everything comes back exactly as you
  left it

**To pull newer code:**
```
cd C:/laragon/www/opencollege
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear
```

---

## If something breaks

| Error | Likely cause | Fix |
|-------|-------------|-----|
| "Site can't be reached" | Laragon not running, or vhost not picked up | Restart Laragon |
| "No application encryption key specified" | Missed Step 5 | Run `php artisan key:generate` |
| "Access denied for user 'root'" | Wrong `.env` password | Step 7 — leave `DB_PASSWORD=` empty |
| "Base table or view not found" | Skipped database creation or migrations | Steps 6 + 8 |
| 500 error after logging in | Corrupted session / stale cache | `php artisan cache:clear && php artisan config:clear && php artisan view:clear` |
| "Class X not found" after `git pull` | New deps not installed | `composer install` |
| Login page has CSRF 419 error | Old session cookie | Clear cookies for `opencollege.test` in browser, or use incognito |
| Composer hangs / times out | Slow internet | Run with `--prefer-source` flag, or retry later |

For any other issue, `storage/logs/laravel.log` has the full error.

---

## Next steps

- Read [`docs/database.md`](database.md) to understand the schema
- Read [`INSTALLATION.md`](../INSTALLATION.md) appendices B–C for
  wildcard DNS and SSL in production
- Read the security docs in [`docs/security/`](security/) before
  deploying beyond localhost
