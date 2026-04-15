# Installation Guide

This guide walks through installing **OpenCollege** on a fresh server or
local development machine. The software is a standard Laravel 12
application, so anyone comfortable with Laravel deployment will find the
steps familiar.

---

## 1. System requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| **PHP** | 8.2 | 8.3 |
| **Composer** | 2.5 | latest |
| **Database** | MySQL 8.0 / MariaDB 10.6 / PostgreSQL 14 | MySQL 8.4 |
| **Node.js** | 18 | 20 LTS |
| **Web server** | Apache 2.4 or Nginx 1.20 | either |
| **Disk** | 2 GB free | 10 GB+ |
| **RAM** | 1 GB | 2 GB+ |

### Required PHP extensions

```
bcmath, ctype, curl, dom, fileinfo, gd, intl, json, mbstring,
openssl, pcre, pdo, pdo_mysql (or pdo_pgsql), tokenizer, xml, zip
```

On Ubuntu/Debian:
```bash
sudo apt install php8.3-{bcmath,curl,gd,intl,mbstring,mysql,xml,zip}
```

## 2. Clone the repository

```bash
git clone https://github.com/PeeapDev/opencollege.git
cd opencollege
```

## 3. Install dependencies

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

For development:
```bash
composer install
npm install
npm run dev
```

## 4. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set at minimum:

```dotenv
APP_NAME=OpenCollege
APP_ENV=production      # or "local" for development
APP_DEBUG=false         # MUST be false in production
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=opencollege
DB_USERNAME=opencollege
DB_PASSWORD=change-me

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@your-domain.example
```

### Dependencies and alternatives (platform independence)

OpenCollege is **vendor-neutral**. Every external service is optional and
replaceable:

| Feature | Default | Alternatives |
|---------|---------|--------------|
| Database | MySQL 8 | MariaDB 10.6+, PostgreSQL 14+ (set `DB_CONNECTION`) |
| Cache / sessions | File | Redis, database, Memcached |
| Queue | Sync | Redis, database, SQS |
| Mail | SMTP | Any Laravel-supported driver |
| File storage | Local filesystem | Any S3-compatible backend |
| Search (optional) | MySQL LIKE | Meilisearch, Algolia, Elasticsearch |

You can run the entire stack on a single commodity Linux VM with no
proprietary cloud dependencies.

## 5. Create the database

```bash
mysql -u root -p -e "CREATE DATABASE opencollege CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p -e "CREATE USER 'opencollege'@'localhost' IDENTIFIED BY 'change-me';"
mysql -u root -p -e "GRANT ALL ON opencollege.* TO 'opencollege'@'localhost';"
```

## 6. Run migrations

```bash
php artisan migrate --force
php artisan storage:link
```

For a development machine with sample data:
```bash
php artisan migrate:fresh --seed
```

## 7. Create the first admin user

```bash
php artisan tinker
```
```php
\App\Models\User::create([
    'name' => 'Administrator',
    'email' => 'admin@your-domain.example',
    'password' => bcrypt('change-me-now'),
]);
```

(Replace with your own bootstrap seeder once implemented.)

## 8. Configure the web server

### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.example;
    root /var/www/opencollege/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache

Ensure `mod_rewrite` is enabled and the document root points to
`/var/www/opencollege/public`. Laravel ships a `public/.htaccess` that
handles the rewrite rules.

## 9. Set correct permissions

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 10. Enable HTTPS

Install a certificate via Let's Encrypt:
```bash
sudo certbot --nginx -d your-domain.example
```

Confirm `.env` has `APP_URL=https://your-domain.example` and
`SESSION_SECURE_COOKIE=true`.

## 11. Schedule the Laravel cron (optional but recommended)

```cron
* * * * * cd /var/www/opencollege && php artisan schedule:run >> /dev/null 2>&1
```

## 12. Start the queue worker (if using async jobs)

```bash
php artisan queue:work --daemon
```
Or supervise it with `supervisord` / systemd — sample config in `docs/deploy/`.

---

## Local development (quick-start)

```bash
git clone https://github.com/PeeapDev/opencollege.git
cd opencollege
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
composer dev        # starts server, queue, logs, vite concurrently
```

Open http://localhost:8000.

---

## Upgrading

```bash
git pull
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Troubleshooting

**"No application encryption key specified"** — run `php artisan key:generate`.

**"Permission denied" on `storage/`** — re-run step 9.

**White page / 500 error** — check `storage/logs/laravel.log` and set
`APP_DEBUG=true` in `.env` temporarily.

**Migrations fail** — ensure the database user has `CREATE`, `ALTER`, `DROP`
on the target database.

For bugs, open an issue at:
https://github.com/PeeapDev/opencollege/issues


---

## Appendix A — Running locally with XAMPP, Laragon, or Herd

If you don't want to manage PHP/MySQL by hand, use one of these bundled
stacks. Pick the one that matches your OS.

### Laragon (Windows — easiest)

1. Download and install **Laragon Full** from https://laragon.org
2. Start Laragon → Apache and MySQL should both show "Running"
3. Clone OpenCollege into Laragon's `www` folder:
   ```
   cd C:\laragon\www
   git clone https://github.com/PeeapDev/opencollege.git
   ```
4. In Laragon, right-click → Quick app → (or it auto-detects) — Laragon
   will automatically create `http://opencollege.test` as a vhost.
5. Open a Laragon terminal (right-click menu → Terminal) and run:
   ```
   cd opencollege
   composer install
   cp .env.example .env
   php artisan key:generate
   ```
6. Create the database: Laragon menu → MySQL → HeidiSQL → right-click
   → Create new → Database → name it `opencollege`
7. Edit `.env`:
   ```
   DB_DATABASE=opencollege
   DB_USERNAME=root
   DB_PASSWORD=
   APP_URL=http://opencollege.test
   ```
8. Run migrations:
   ```
   php artisan migrate --seed
   ```
9. Open http://opencollege.test in your browser — done.

Laragon auto-creates `.test` domains and gives you Apache, MySQL, PHP,
Node, Composer, Git, and phpMyAdmin/HeidiSQL in one click. It's the
fastest path on Windows.

### XAMPP (Windows / macOS / Linux)

1. Download XAMPP from https://www.apachefriends.org
2. Install and start Apache + MySQL from the XAMPP control panel
3. Clone OpenCollege into `htdocs`:
   ```
   cd /path/to/xampp/htdocs
   git clone https://github.com/PeeapDev/opencollege.git
   ```
4. Open a terminal and run:
   ```
   cd opencollege
   composer install
   cp .env.example .env
   php artisan key:generate
   ```
5. Create the database via http://localhost/phpmyadmin → "New" → name it
   `opencollege` → utf8mb4_unicode_ci
6. Edit `.env`:
   ```
   DB_DATABASE=opencollege
   DB_USERNAME=root
   DB_PASSWORD=
   APP_URL=http://localhost:8000
   ```
7. Run migrations and start the dev server:
   ```
   php artisan migrate --seed
   php artisan serve
   ```
8. Open http://localhost:8000

> **Note:** XAMPP ships PHP in `C:\xampp\php\` (Windows) or
> `/opt/lampp/bin/` (Linux). Make sure that directory is on your PATH, or
> call PHP with its full path.

### Herd (macOS / Windows)

Herd from https://herd.laravel.com ships a pre-tuned PHP + nginx + DBngin
bundle designed for Laravel development. If you already use Herd:

```
cd ~/Herd
git clone https://github.com/PeeapDev/opencollege.git
cd opencollege
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Open `http://opencollege.test` — Herd auto-creates the vhost.

### Docker Compose (any OS)

If you prefer containers:

```
git clone https://github.com/PeeapDev/opencollege.git
cd opencollege
docker compose up -d
```

A `docker-compose.yml` ships in the repo root (coming in the next
release). First run will build the image, start MySQL, and run
migrations automatically.

### Laravel Sail (Linux / WSL2)

Sail is Docker Compose with Laravel's ergonomics:

```
git clone https://github.com/PeeapDev/opencollege.git
cd opencollege
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

---

## Appendix B — Requirements check script

Before you start, run:

```bash
php -v
php -m | grep -Ei 'bcmath|ctype|curl|dom|gd|intl|mbstring|openssl|pdo_mysql|xml|zip'
composer --version
node --version
mysql --version
```

If any of those return errors or missing extensions, see Section 1.


---

## Appendix C — Wildcard setup for multi-tenancy

OpenCollege uses **subdomain-based multi-tenancy**: each college lives
at `{slug}.your-domain.example` (e.g. `fourah-bay.college.edu.sl`,
`njala.college.edu.sl`). For this to work you must configure **three
layers** to respond to `*.your-domain.example`:

1. **DNS** — a wildcard A record so any subdomain resolves to your server
2. **Web server vhost** — a wildcard `ServerAlias` / `server_name` so
   the server accepts any subdomain
3. **SSL certificate** — a wildcard cert so HTTPS works on every subdomain

Any one of these missing breaks tenancy silently. Skip any of them and
individual colleges will 404 or show the main site instead of their tenant.

---

### C.1 DNS — wildcard A record

Add an `A` record at your DNS provider:

```
Type   Name   Value
----   ----   --------------
A      *      203.0.113.45      (your server IP)
A      @      203.0.113.45      (the root — for main site)
```

Verify:
```bash
dig +short random-test.your-domain.example
# Should return your server IP
dig +short another-random.your-domain.example
# Should also return your server IP
```

**cPanel / Plesk users:** add a DNS record with host `*` in the zone
editor. Some shared hosts disable wildcard DNS — ask your provider if
`*` isn't accepted.

**Cloudflare users:** wildcard DNS records are free but **wildcard proxy
(orange cloud)** requires a paid plan. If you're on the free tier, set
the wildcard record to **DNS-only (grey cloud)** and point it directly
to your origin.

---

### C.2 Apache — wildcard vhost

#### On a cPanel server (recommended for shared hosting)

1. Log into cPanel → **Domains → Subdomains** → add a subdomain:
   - **Subdomain:** `*`
   - **Domain:** `your-domain.example`
   - **Document root:** the same `public_html/` as your main site
2. Edit the vhost directly (if cPanel lets you) or ask WHM support to
   add `ServerAlias *.your-domain.example` to your vhost

On many cPanel servers the wildcard subdomain form works out of the
box — after adding it, `test.your-domain.example` immediately resolves
to the same Laravel installation as the main domain.

#### On a VPS with your own Apache config

```apache
<VirtualHost *:443>
    ServerName your-domain.example
    ServerAlias *.your-domain.example
    DocumentRoot /var/www/opencollege/public

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/your-domain.example/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/your-domain.example/privkey.pem

    <Directory /var/www/opencollege/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Reload: `sudo apachectl configtest && sudo systemctl reload apache2`

### C.3 Nginx — wildcard server block

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.example *.your-domain.example;
    root /var/www/opencollege/public;

    ssl_certificate     /etc/letsencrypt/live/your-domain.example/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.example/privkey.pem;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
}
```

Reload: `sudo nginx -t && sudo systemctl reload nginx`

---

### C.4 SSL — wildcard certificate (Let's Encrypt)

Let's Encrypt issues free wildcard certificates but **only via DNS-01
challenge** (HTTP challenge won't work for wildcards). You need your
DNS provider's API to automate it.

#### Using certbot with Cloudflare DNS

```bash
sudo apt install certbot python3-certbot-dns-cloudflare

# Create credentials file
sudo mkdir -p /etc/letsencrypt
echo "dns_cloudflare_api_token = YOUR_CLOUDFLARE_API_TOKEN" \
    | sudo tee /etc/letsencrypt/cloudflare.ini
sudo chmod 600 /etc/letsencrypt/cloudflare.ini

# Issue the cert
sudo certbot certonly \
    --dns-cloudflare \
    --dns-cloudflare-credentials /etc/letsencrypt/cloudflare.ini \
    -d your-domain.example \
    -d '*.your-domain.example' \
    --agree-tos -m you@example.com --non-interactive
```

Certificates auto-renew via systemd timer — verify with
`sudo certbot renew --dry-run`.

#### Using acme.sh (no Python dependencies)

```bash
curl https://get.acme.sh | sh -s email=you@example.com
export CF_Token="YOUR_CLOUDFLARE_API_TOKEN"
~/.acme.sh/acme.sh --issue --dns dns_cf \
    -d your-domain.example \
    -d '*.your-domain.example'
```

#### Other DNS providers

`certbot-dns-*` plugins exist for Route 53, DigitalOcean, Namecheap,
Gandi, Google Cloud DNS, Hetzner, and many more. See
https://eff-certbot.readthedocs.io/en/latest/using.html#dns-plugins

---

### C.5 Local development — wildcard subdomains on localhost

Multi-tenant testing locally is harder because Windows / macOS hosts
files **don't support wildcards**.

#### Laragon (Windows) — built-in wildcard support

Laragon handles this automatically.

1. Laragon → menu → **Preferences → Services & Ports** → enable
   **"Auto create virtual hosts"** and **"Auto add 127.0.0.1 your-app.test
   to hosts file"**
2. Drop OpenCollege into `C:\laragon\www\opencollege`
3. Restart Laragon
4. `opencollege.test` is auto-created
5. For wildcard subdomains, edit `C:\laragon\etc\apache2\sites-enabled\auto.opencollege.test.conf`
   and add:
   ```
   ServerAlias *.opencollege.test
   ```
6. Install **Acrylic DNS Proxy** (Laragon → Preferences → Services &
   ports → check "Acrylic DNS Proxy" → Apply)
7. Edit `C:\Program Files (x86)\Acrylic DNS Proxy\AcrylicHosts.txt`
   and add:
   ```
   127.0.0.1 *.opencollege.test
   ```
8. Restart Acrylic, then you can visit `demo.opencollege.test`,
   `njala.opencollege.test`, etc. — all resolve to the same Laravel app.

#### XAMPP (Windows / macOS / Linux)

No automatic wildcard — pick one of:

**Option 1 — add each tenant manually to the hosts file**
```
# C:\Windows\System32\drivers\etc\hosts   (or /etc/hosts on macOS/Linux)
127.0.0.1 opencollege.test
127.0.0.1 demo.opencollege.test
127.0.0.1 njala.opencollege.test
```
Not elegant but works for a few test tenants.

**Option 2 — run a local wildcard DNS resolver**

macOS / Linux — use `dnsmasq`:
```bash
brew install dnsmasq   # macOS
echo 'address=/opencollege.test/127.0.0.1' >> /usr/local/etc/dnsmasq.conf
sudo brew services start dnsmasq
```
Then point macOS at it: **System Settings → Network → DNS → add
`127.0.0.1`**.

Windows — use **Acrylic DNS Proxy** as described in the Laragon section.

**XAMPP Apache vhost:**

Edit `xampp/apache/conf/extra/httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    ServerName opencollege.test
    ServerAlias *.opencollege.test
    DocumentRoot "C:/xampp/htdocs/opencollege/public"
    <Directory "C:/xampp/htdocs/opencollege/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
Then restart Apache from the XAMPP control panel.

#### Herd (macOS / Windows)

Herd auto-creates `.test` vhosts but wildcard requires manual config:

```bash
cd ~/Herd/opencollege
# Herd reads the public/ directory automatically
# For wildcard, run:
herd park
herd link opencollege
```

Then edit Herd's Caddyfile (Herd → Advanced → Edit Caddyfile) and add:
```
*.opencollege.test {
    root * /Users/you/Herd/opencollege/public
    php_fastcgi 127.0.0.1:9000
    file_server
    encode gzip
    tls internal
}
```

Herd uses CoreDNS internally so wildcard works after Caddy reload.

#### Simplest possible local workaround — sslip.io / nip.io

If you don't want to touch DNS at all, use the public wildcard DNS
services **nip.io** or **sslip.io** which resolve any
`{anything}.{IP}.nip.io` to that IP:

```
http://opencollege.127.0.0.1.nip.io
http://njala.opencollege.127.0.0.1.nip.io
http://demo.opencollege.127.0.0.1.nip.io
```

All of these hit your local server at 127.0.0.1. No hosts file, no
DNS server, no certbot. Works for local testing only — the URLs are
ugly but it's the zero-config option.

---

### C.6 OpenCollege side — tenant resolution

Once DNS + vhost + SSL are in place, OpenCollege resolves tenants via
its `TenantMiddleware` (`app/Http/Middleware/TenantMiddleware.php`),
which:

1. Reads the current host from the incoming request
2. Strips the central domain (configured in `.env` as `APP_URL`)
3. Looks up the remaining subdomain in the `institutions.domain` column
4. Sets the current tenant context for the rest of the request

So if `APP_URL=https://college.edu.sl` and a user visits
`https://njala.college.edu.sl/dashboard`, the middleware queries
`institutions WHERE domain = 'njala'` and scopes all subsequent
queries to that institution via the `BelongsToInstitution` trait.

**Adding a new tenant:** use the SuperAdmin UI
(`/superadmin/colleges/create`) or seed via `InstitutionSeeder`. The
`domain` field is what determines the subdomain — pick short slugs
like `njala`, `fbc`, `usl`.

---

### C.7 Troubleshooting

**"Subdomain resolves but shows main site":**
- Wildcard DNS works but vhost `ServerAlias *.your-domain.example` is
  missing. Add it and reload the web server.

**"Subdomain shows 404 from OpenCollege":**
- DNS and vhost are fine — the tenant `domain` doesn't exist in the
  `institutions` table, or is marked inactive. Check
  `SELECT domain, active FROM institutions;`.

**"HTTPS works on main domain but breaks on subdomain":**
- You issued a non-wildcard certificate. Re-issue with `-d '*.your-domain.example'`
  as shown in C.4.

**"nip.io / sslip.io doesn't work":**
- Some corporate DNS servers block wildcard domains. Try from your
  phone on mobile data — if that works, your office DNS is filtering.

**"Works on Laragon but not on production VPS":**
- You're missing layer 1 (DNS wildcard). The vhost alone isn't enough
  — the browser never reaches your server because the subdomain
  doesn't resolve. `dig` from the VPS itself to verify.
