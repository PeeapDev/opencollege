# OpenCollege

**Open-source College & University Management System for Sierra Leone and beyond**

Built with Laravel 12 &middot; Modular Architecture &middot; Multi-tenant &middot; DPG Compliant

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![DPG Standard](https://img.shields.io/badge/DPG-Compliant-green.svg)](https://digitalpublicgoods.net)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Laravel 12](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)

---

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Modules](#modules)
- [Architecture](#architecture)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Multi-Tenancy](#multi-tenancy)
- [NSI Verification](#nsi-verification--academic-history)
- [PeeapPay Integration](#peeappay-integration)
- [Tech Stack](#tech-stack)
- [API Reference](#api-reference)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [Security](#security)
- [License](#license)
- [DPG Standard Compliance](#dpg-standard-compliance)
- [Relevant SDGs](#relevant-sustainable-development-goals)
- [Acknowledgements](#acknowledgements)

---

## Overview

OpenCollege is a comprehensive, modular, multi-tenant college and university management platform designed for developing countries. It enables institutions to manage academics, students, staff, finance, examinations, HR, and communications from a single unified platform.

Built as a **Digital Public Good**, it is free to use, modify, and deploy. OpenCollege addresses the lack of affordable, locally-relevant higher education management software in Sub-Saharan Africa by providing an open-source alternative that institutions can self-host and customize.

### Key Features

- **Multi-Tenant Architecture** -- One installation serves multiple colleges via subdomains (e.g., `njala.college.edu.sl`, `csl.college.edu.sl`)
- **Super Admin Panel** -- Platform-wide management at the main domain with oversight of all tenant colleges
- **NSI Verification Bridge** -- Connects to Sierra Leone's SDSL school management system to verify students' full academic history from primary through secondary school using the National Student Identifier (NSI)
- **PeeapPay Integration** -- Online payment gateway supporting mobile money and bank transfers
- **Student Portal** -- Self-service portal for students to view results, finances, and download ID cards
- **Online Admissions** -- Public-facing admission forms with application tracking
- **QR Code ID Cards** -- Generate student ID cards with QR codes for verification
- **Modular Design** -- Each functional area is an independent module with its own controllers, models, views, routes, and migrations
- **DPG Registry Compliant** -- Open source, documented, standards-compliant

---

## Modules

| Module | Alias | Description |
|--------|-------|-------------|
| **Core** | `core` | Authentication, multi-tenancy, dashboard, NSI verification, super admin, frontend pages, layouts |
| **Academic** | `academic` | Faculties, departments, programs, courses, semesters, academic years |
| **Student** | `student` | Enrollment, admissions, student portal, ID cards with QR codes, student records |
| **Staff** | `staff` | Academic and non-academic staff records, designations, teaching assignments |
| **Finance** | `finance` | Invoices, payments, fee categories, scholarships, PeeapPay online payments |
| **Exam** | `exam` | Exam scheduling, grading, result stores, result publications, exam board |
| **Attendance** | `attendance` | Student and staff attendance records, reports, analytics |
| **Library** | `library` | Book catalog, categories, issue/return tracking, reports |
| **Communication** | `communication` | Notices, internal messaging, announcements |
| **HumanResource** | `hr` | Leave management, payroll, staff directory, HR dashboard |
| **Settings** | `settings` | Institution configuration, roles, permissions, system preferences |

Each module contains a `module.json` manifest that defines its name, version, dependencies, and activation status. See [docs/modules.md](docs/modules.md) for detailed module documentation.

---

## Architecture

```
opencollege/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   │       ├── TenantMiddleware.php      # Subdomain-based tenant identification
│   │       └── SuperAdminMiddleware.php   # Super admin access control
│   ├── Models/
│   │   └── User.php                      # Base user model with multi-tenancy
│   ├── Modules/
│   │   ├── Core/                         # Priority 0 -- loaded first
│   │   │   ├── Controllers/
│   │   │   ├── Middleware/
│   │   │   ├── Providers/
│   │   │   │   └── ModuleServiceProvider.php  # Auto-discovers & loads all modules
│   │   │   ├── Routes/
│   │   │   ├── Traits/
│   │   │   │   └── BelongsToInstitution.php   # Multi-tenant scoping trait
│   │   │   ├── Views/
│   │   │   └── module.json
│   │   ├── Academic/                     # Faculties, departments, programs, courses
│   │   ├── Student/                      # Enrollment, admissions, portal, ID cards
│   │   ├── Staff/                        # Staff records, designations
│   │   ├── Finance/                      # Invoices, payments, PeeapPay
│   │   │   └── Services/
│   │   │       └── PeeapPayService.php   # Payment gateway integration
│   │   ├── Exam/                         # Exams, grading, results, CGPA
│   │   ├── Attendance/                   # Attendance tracking
│   │   ├── Library/                      # Library management
│   │   ├── Communication/                # Notices, messaging
│   │   ├── HumanResource/               # HR, leave, payroll
│   │   └── Settings/                     # System configuration, RBAC
│   └── Providers/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
│       └── OpenCollegeSeeder.php         # Sample data with 2 colleges
├── public/
│   └── index.php                         # Application entry point
├── resources/
├── routes/
├── storage/
├── tests/
├── docs/
│   └── modules.md                        # Module system documentation
├── .env.example
├── composer.json
└── README.md
```

### Module Discovery

The `ModuleServiceProvider` auto-discovers modules by scanning for `module.json` manifests in each module directory. Modules are loaded in priority order (lower number = higher priority). New modules can be added by:

1. Creating a directory under `app/Modules/YourModule/`
2. Adding a `module.json` manifest
3. Creating `Controllers/`, `Routes/web.php`, `Views/`, and `Migrations/` as needed

No manual registration required -- the service provider discovers and loads active modules automatically.

---

## Requirements

| Component | Version |
|-----------|---------|
| PHP | 8.2 or higher |
| MySQL | 8.0 or higher (or MariaDB 10.5+) |
| Composer | 2.0 or higher |
| Node.js | 18+ (optional, for asset compilation) |
| Web Server | Apache 2.4+ or Nginx 1.18+ |

### Required PHP Extensions

- BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, cURL

---

## Installation

### Quick Start

```bash
# Clone the repository
git clone https://github.com/PeeapDev/opencollege.git
cd opencollege

# Install PHP dependencies
composer install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Create an empty database first (see below), then configure
# the credentials in .env

# Run migrations and seed sample data
php artisan migrate --seed

# Start the development server
php artisan serve
```

> **⚠️ Before `php artisan migrate`: create the database.**
> Laravel will create the *tables*, but not the database itself.
>
> **New to this?** Follow the step-by-step walkthrough:
> - **Windows + Laragon (recommended):**
>   [`docs/install-laragon.md`](docs/install-laragon.md) — 15 numbered
>   steps, ~20 minutes, no prior PHP knowledge needed
> - **Any OS / any stack:** [`docs/database.md`](docs/database.md)
>   covers Laragon, XAMPP, standalone MySQL, PostgreSQL + troubleshooting
>
> **Common beginner mistake:** pasting SQL (`CREATE DATABASE …`) into
> PowerShell. Those are SQL statements, not shell commands — they go
> inside a MySQL client (HeidiSQL, phpMyAdmin, or the `mysql` CLI
> after running `mysql -u root -p`).

### Using the Composer Script

```bash
git clone https://github.com/PeeapDev/opencollege.git
cd opencollege
composer setup
```

This runs `composer install`, copies `.env.example`, generates the app key, runs migrations, installs npm packages, and builds frontend assets.

### Default Login Credentials

After seeding, the following accounts are available:

| Role | Email | Password | Domain |
|------|-------|----------|--------|
| Super Admin | admin@college.edu.sl | admin123 | Main domain |
| College Admin (CSL) | admin@csl.college.edu.sl | college123 | csl.college.edu.sl |
| College Admin (Njala) | admin@njala.college.edu.sl | college123 | njala.college.edu.sl |
| Sample Student | alhaji.turay@student.college.edu.sl | student123 | Student portal |
| Sample Staff | mkamara@college.edu.sl | staff123 | Staff dashboard |

> **Important:** Change these default passwords immediately in production environments.

---

## Configuration

### Related documentation

| Topic | File |
|-------|------|
| **Windows step-by-step install with Laragon** | [`docs/install-laragon.md`](docs/install-laragon.md) |
| Database schema, seeder, multi-tenancy, backups | [`docs/database.md`](docs/database.md) |
| Deployment & CI/CD | [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) |
| Full installation guide (XAMPP, Docker, wildcard DNS, SSL) | [`INSTALLATION.md`](INSTALLATION.md) |
| Security architecture (audit logs, 2FA, session timeout, breach runbook) | [`docs/security/`](docs/security/) |
| Privacy policy | [`PRIVACY.md`](PRIVACY.md) |
| Contributing | [`CONTRIBUTING.md`](CONTRIBUTING.md) |
| Authors / maintainers | [`AUTHORS.md`](AUTHORS.md) |

### Environment Variables

Copy `.env.example` to `.env` and configure the following:

```env
# Application
APP_NAME=OpenCollege
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=opencollege
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# SDSL Integration (NSI Verification)
SDSL_API_URL=https://gov.school.edu.sl/api
SDSL_API_KEY=your_sdsl_api_key

# PeeapPay Payment Gateway
PEEAPPAY_BASE_URL=https://api.peeappay.com/v1
PEEAPPAY_API_KEY=your_api_key
PEEAPPAY_MERCHANT_ID=your_merchant_id
PEEAPPAY_SECRET_KEY=your_secret_key

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Database Setup

OpenCollege supports MySQL 8.0+ and MariaDB 10.5+. Create a database:

```sql
CREATE DATABASE opencollege CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'opencollege'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON opencollege.* TO 'opencollege'@'localhost';
FLUSH PRIVILEGES;
```

---

## Multi-Tenancy

OpenCollege uses **subdomain-based multi-tenancy**:

- **Main domain** (`college.edu.sl`) -- Super Admin with full platform management
- **Subdomains** (`njala.college.edu.sl`) -- College-scoped management only

### How It Works

1. The `TenantMiddleware` intercepts every request
2. It extracts the subdomain from the request host
3. It looks up the corresponding `Institution` record
4. All subsequent database queries are scoped to that institution's `institution_id`

### Setting Up Multi-Tenancy

1. Configure wildcard DNS: `*.yourdomain.com` pointing to your server
2. Register colleges via the Super Admin panel at the main domain
3. Each college gets a unique subdomain automatically

### Data Isolation

All module models use the `BelongsToInstitution` trait which automatically scopes queries by `institution_id`, ensuring complete data isolation between tenant colleges.

---

## NSI Verification & Academic History

The **National Student Identifier (NSI)** bridges Sierra Leone's school system (SDSL) with OpenCollege. When an NSI is verified:

1. OpenCollege calls the SDSL API at `gov.school.edu.sl`
2. SDSL returns the student's **full academic journey** -- every class attended from primary through SSS3
3. Results are grouped by **external exam milestones**:
   - **Class 6 / Grade 6** -- NPSE (National Primary School Examination)
   - **JSS 3 / JHS 3** -- BECE (Basic Education Certificate Examination)
   - **SSS 3** -- WASSCE (West African Senior School Certificate Examination)
4. Subject grades, marks, GPAs, and aggregate scores are displayed in a comprehensive timeline view
5. Verification results are stored in the `nsi_verifications` table for future reference

This allows colleges to instantly verify a student's complete pre-tertiary academic record during the admission process.

---

## PeeapPay Integration

The Finance module integrates with **PeeapPay** for online payments:

- Students can pay invoices online via mobile money or bank transfer
- Transaction tracking with status monitoring (pending, success, failed)
- Webhook support for real-time payment confirmation
- Automatic invoice balance updates upon successful payment
- Full transaction history with receipt generation

### Payment Flow

1. Admin or student clicks "Pay Online" on an invoice
2. `PeeapPayController` initializes a transaction with the PeeapPay API
3. User is redirected to PeeapPay checkout (supports mobile money and bank transfer)
4. On completion, the callback URL verifies the transaction
5. Webhook provides real-time confirmation
6. Payment record is created and invoice balance updated automatically

---

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend Framework | Laravel 12 (PHP 8.2+) |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Charts | ApexCharts |
| Icons | Font Awesome 6 |
| Database | MySQL 8+ / MariaDB 10.5+ |
| Authentication | Laravel Session (Web) + Sanctum (API) |
| Payment Gateway | PeeapPay |
| External APIs | SDSL School Management System |
| Build Tool | Vite |
| Testing | PHPUnit 11.5+ |
| Code Style | Laravel Pint (PSR-12) |

---

## API Reference

OpenCollege provides a RESTful API for integration with external systems.

### Authentication

API endpoints use Laravel Sanctum token authentication. Obtain a token via:

```bash
POST /api/login
Content-Type: application/json

{
  "email": "admin@college.edu.sl",
  "password": "your_password"
}
```

### Module APIs

Each module can expose API routes via its `Routes/api.php` file. API routes are automatically loaded by the module service provider.

For detailed API documentation, see [docs/modules.md](docs/modules.md).

---

## Deployment

### Shared Hosting (cPanel)

1. Upload all files to your hosting account
2. Point the document root to the `public/` directory
3. Create a MySQL database via cPanel
4. Configure `.env` with production credentials
5. Run migrations: `php artisan migrate --seed`
6. Set up wildcard subdomain in cPanel for multi-tenancy

### VPS / Dedicated Server

```bash
# Clone and install
git clone https://github.com/PeeapDev/opencollege.git /var/www/opencollege
cd /var/www/opencollege
composer install --no-dev --optimize-autoloader

# Configure environment
cp .env.example .env
php artisan key:generate
# Edit .env with production values

# Run migrations
php artisan migrate --seed

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Apache Virtual Host

```apache
<VirtualHost *:80>
    ServerName college.edu.sl
    ServerAlias *.college.edu.sl
    DocumentRoot /var/www/opencollege/public

    <Directory /var/www/opencollege/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name college.edu.sl *.college.edu.sl;
    root /var/www/opencollege/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### DNS Configuration

For multi-tenancy, set up a wildcard DNS record:

```
*.college.edu.sl    A    YOUR_SERVER_IP
college.edu.sl      A    YOUR_SERVER_IP
```

---

## Contributing

We welcome contributions from the community. Please read our [Contributing Guide](CONTRIBUTING.md) before submitting changes.

### Quick Start for Contributors

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Make your changes following [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
4. Write or update tests as needed
5. Commit with clear messages: `git commit -m "Add: brief description"`
6. Push to your fork and open a Pull Request

### Creating a New Module

See [docs/modules.md](docs/modules.md) for the complete module development guide.

---

## Security

If you discover a security vulnerability, please report it responsibly. See [SECURITY.md](SECURITY.md) for our security policy and reporting instructions.

**Do not report security vulnerabilities through public GitHub issues.**

---

## License

OpenCollege is open-source software licensed under the [MIT License](LICENSE).

You are free to use, modify, and distribute this software for personal, educational, and commercial purposes.

---

## DPG Standard Compliance

OpenCollege is designed to meet the [Digital Public Goods Standard](https://digitalpublicgoods.net/standard/) across all nine indicators:

| # | Indicator | Status | Evidence |
|---|-----------|--------|----------|
| 1 | **Relevance to SDGs** | Achieved | Directly supports SDG 4 (Quality Education) and SDG 10 (Reduced Inequalities) |
| 2 | **Use of Approved Open License** | Achieved | MIT License ([LICENSE](LICENSE)) |
| 3 | **Clear Ownership** | Achieved | Developed and maintained by PeeapDev |
| 4 | **Platform Independence** | Achieved | Standard PHP/MySQL stack, runs on any LAMP/LEMP server |
| 5 | **Documentation** | Achieved | README, module docs, API docs, contributing guide |
| 6 | **Mechanism for Extracting Data** | Achieved | REST API, database export, no vendor lock-in |
| 7 | **Adherence to Privacy and Applicable Laws** | Achieved | Tenant-isolated data, no external tracking, configurable data retention |
| 8 | **Adherence to Standards & Best Practices** | Achieved | PSR-12, REST API, JSON, UTF-8, semantic versioning |
| 9 | **Does No Harm** | Achieved | Education-focused, privacy-respecting, secure by default |

### Data Privacy & Protection

- All data is tenant-isolated -- colleges cannot access each other's data
- No external analytics or tracking services
- No data is shared with third parties without explicit configuration
- Passwords are hashed using bcrypt with configurable rounds
- Session data is encrypted and stored server-side
- Student data is protected under institutional data policies

### Open Standards Used

- **HTTP/REST** -- All API communication follows REST conventions
- **JSON** -- Standard data interchange format
- **UTF-8** -- Full Unicode support for international character sets
- **PSR-12** -- PHP coding standard
- **Semantic Versioning** -- Version numbering follows semver.org
- **OAuth 2.0** -- Payment gateway authentication (PeeapPay)

---

## Relevant Sustainable Development Goals

OpenCollege directly contributes to the following UN Sustainable Development Goals:

### SDG 4: Quality Education
- Provides affordable, accessible college management tools for institutions in developing countries
- Enables better academic record-keeping and student outcome tracking
- Supports transparent examination and grading processes

### SDG 10: Reduced Inequalities
- Eliminates the cost barrier of proprietary college management software
- Enables institutions with limited budgets to digitize their operations
- NSI bridge ensures continuity of academic records across educational levels

### SDG 9: Industry, Innovation and Infrastructure
- Builds resilient digital infrastructure for higher education
- Promotes innovation through open-source, modular architecture
- Integrates with local payment systems (mobile money via PeeapPay)

### SDG 16: Peace, Justice and Strong Institutions
- Promotes transparent and accountable institutional management
- Supports proper record-keeping and audit trails
- Enables data-driven decision-making in educational governance

### SDG 17: Partnerships for the Goals
- Open-source model encourages collaboration between institutions
- API-first design enables integration with national education systems
- Multi-tenant architecture allows resource sharing across institutions

---

## Acknowledgements

- **[PeeapDev](https://github.com/PeeapDev)** -- Core development and maintenance
- **School District Sierra Leone (SDSL)** -- NSI verification API and school management system
- **[PeeapPay](https://peeappay.com)** -- Payment gateway integration
- **[Laravel](https://laravel.com)** -- The PHP framework that powers OpenCollege
- **All contributors** -- Thank you for helping make education technology accessible to all

---

**Developed by [PeeapDev](https://github.com/PeeapDev)** as part of the School District Sierra Leone ecosystem.
