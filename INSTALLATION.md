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
