# Database Guide

This document describes the OpenCollege database: what tables exist, how
they relate, what migrations create them, and what the seeder populates.

---

## Supported engines

OpenCollege uses standard SQL via Laravel's Eloquent ORM. Tested on:

| Engine | Version | Status |
|--------|---------|--------|
| MySQL | 8.0+ | Primary (what production uses) |
| MariaDB | 10.6+ | Supported |
| PostgreSQL | 14+ | Supported |
| SQLite | 3.35+ | Dev / testing only — do not use in production (no concurrent writes) |

Character set: **utf8mb4** with **utf8mb4_unicode_ci** collation. This
matters for student names in non-Latin scripts, emoji-safe text, and
proper case-insensitive matching.

## Creating the database before `migrate`

You need to create an empty database **before** running migrations.
Laravel will create tables inside it, but it won't create the database
itself.

> ⚠️ The SQL commands below are **NOT shell commands**. Do not paste
> them into PowerShell, Git Bash, or a Mac/Linux terminal — they will
> error. They run **inside a MySQL client**.

Pick ONE of the following paths depending on your setup:

### Path 1 — Laragon (Windows, recommended for beginners)

Laragon bundles MySQL + a GUI database tool (HeidiSQL). You do not need
to type any SQL.

1. Open **Laragon**
2. Click **Start All** (Apache + MySQL turn green)
3. Right-click the Laragon window → **Database → HeidiSQL** → Open
4. In HeidiSQL, right-click in the left panel (where servers are listed)
   → **Create new → Database**
5. Fill in:
   - **Name:** `opencollege`
   - **Collation:** `utf8mb4_unicode_ci`
6. Click **OK** — done.

Laragon's default MySQL user is `root` with **empty password**, so put
this in your `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=opencollege
DB_USERNAME=root
DB_PASSWORD=
```

### Path 2 — XAMPP / MAMP / WAMP (Windows / macOS)

These bundle MySQL + phpMyAdmin (web GUI).

1. Start MySQL from the control panel
2. Open **http://localhost/phpmyadmin** in your browser
3. Click **New** in the left sidebar
4. Fill:
   - **Database name:** `opencollege`
   - **Collation:** `utf8mb4_unicode_ci`
5. Click **Create**

Default credentials in XAMPP: `root` / empty password. Put the same
`.env` values shown in Path 1.

### Path 3 — Standalone MySQL on Linux / macOS / Windows

If you installed MySQL directly (not through a bundle), open a terminal
and **start the MySQL client** first — the `mysql` program logs you
into the server so you can run SQL.

```bash
# This single line prompts for your MySQL root password, then runs the SQL:
mysql -u root -p -e "CREATE DATABASE opencollege CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

That one-liner is all you need if you're happy using `root` from the
app. For better isolation, create a dedicated app user:

```bash
# Log into the MySQL shell. You'll see a 'mysql>' prompt.
mysql -u root -p
```

Once you see `mysql>`, paste the SQL (press Enter after each line — each
statement ends with a semicolon):

```sql
CREATE DATABASE opencollege CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'opencollege'@'localhost' IDENTIFIED BY 'change-me-please';
GRANT ALL ON opencollege.* TO 'opencollege'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

`EXIT;` returns you to the normal terminal prompt. Then in your `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=opencollege
DB_USERNAME=opencollege
DB_PASSWORD=change-me-please
```

### Path 4 — PostgreSQL (instead of MySQL)

```bash
sudo -u postgres psql
```

In the `psql` shell:

```sql
CREATE DATABASE opencollege;
CREATE USER opencollege WITH PASSWORD 'change-me-please';
GRANT ALL PRIVILEGES ON DATABASE opencollege TO opencollege;
\q
```

`.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=opencollege
DB_USERNAME=opencollege
DB_PASSWORD=change-me-please
```

### Common mistakes

| Symptom | Cause | Fix |
|---------|-------|-----|
| `CREATE : The term 'CREATE' is not recognized` (PowerShell) | You pasted SQL into PowerShell instead of the MySQL client. | See Path 3 — run `mysql -u root -p` first to enter the SQL shell. |
| `Access denied for user 'root'@'localhost'` | Wrong password, or MySQL not started. | Reset MySQL root password, or start Laragon/XAMPP first. |
| `Unknown database 'opencollege'` when running migrations | You skipped this step entirely. | Go back and create the database. |
| `SQLSTATE[HY000] [2002] No such file` | MySQL isn't running. | Start it (Laragon → Start All, XAMPP → Start, `sudo systemctl start mysql`). |
| `Character set 'utf8mb4_unicode_ci' is not a compatible ... ` | Old MySQL version. | Upgrade to MySQL 8+ or MariaDB 10.6+. Older versions use different collation names. |

Once the database exists and `.env` points to it, run migrations:

```bash
php artisan migrate --seed
```

---

## Migrations — where they live

Laravel migrations are split across two locations:

1. **`database/migrations/`** — framework-level and cross-cutting tables
   (users, cache, sessions, jobs, audit logs, admissions, id_cards,
   frontend_settings, exam_schedules, nsi_verifications, safety net).
2. **`app/Modules/*/Migrations/`** — per-module tables, auto-discovered
   by `ModuleServiceProvider`. Each module owns its domain tables.

Running `php artisan migrate` executes both locations in timestamp order
(cross-module dependencies are carefully ordered by filename prefix).

### Migration execution order (key moments)

```
0001_01_01_*                    — Laravel defaults (users, cache, jobs)
2026_01_01_000001_*             — institutions (Settings module)
2026_01_01_000002_*             — roles, permissions, user_roles (Settings)
2026_01_01_000010_*             — academic_years, faculties, departments,
                                  programs, courses, semesters (Academic)
2026_01_01_000020_*             — students, enrollments, grades, cgpa_records
2026_01_01_000030_*             — staff, teaching_assignments, leave_requests
2026_01_01_000040_*             — fee_structures, invoices, payments,
                                  invoice_items, scholarships, fee_categories
2026_01_01_000050_*             — attendances, staff_attendances
2026_01_01_000060_*             — exams, exam_types, grade_scales
2026_01_01_000070_*             — books, book_categories, book_issues
2026_01_01_000080_*             — messages, notices, notifications
2026_01_01_*                    — cross-cutting: add_institution_to_users
2026_01_02_*                    — nsi_verifications, multitenant columns
2026_01_03_*                    — admissions, id_cards, frontend_settings,
                                  exam_schedules, result_publications
2026_03_*                       — hr_tables, peeappay_transactions
2026_04_17_*                    — audit_logs + safety-net columns
                                  (must_change_password, locked_until,
                                  last_login_at, soft-delete on institutions)
```

Run `php artisan migrate:status` to see which migrations have been
applied on any environment.

---

## Schema overview — by domain

### Platform & access control

| Table | Purpose |
|-------|---------|
| `institutions` | Every tenant (plus the special id=1 for the HEMIS platform). Holds name, code, domain (subdomain slug), custom_domain, type (college/polytechnic/university), accreditation_status, plan, max_students, active, deleted_at (soft). |
| `users` | All users across all tenants. Carries `current_institution_id`, safety-net columns (`must_change_password`, `failed_login_attempts`, `locked_until`, `last_login_at`, `last_login_ip`). |
| `roles` | Tenant-scoped roles (super_admin, admin, registrar, lecturer, student, librarian, accountant, plus custom). |
| `permissions` | Granular permission strings (e.g. `student.create`, `grade.edit`). |
| `role_permissions` | Many-to-many. |
| `user_roles` | Many-to-many with pivot `institution_id` (a user can have different roles in different tenants). |
| `audit_logs` | Append-only who-did-what trail. Written by `App\Traits\LogsAudit` on User, Institution, Grade, Invoice, Payment. |

### Academic structure

| Table | Purpose |
|-------|---------|
| `faculties` | Top-level academic units (Faculty of Science, etc.) |
| `departments` | Under faculties (CS dept, etc.) |
| `programs` | Degree programs under departments. Has `level` enum (certificate / diploma / higher_diploma / bachelors / masters / doctorate), `duration_years`, `total_credits`. |
| `courses` | Individual course offerings. |
| `program_courses` | Many-to-many — which courses are required/elective for which programs. |
| `course_sections` | A specific offering of a course in a semester. |
| `academic_years` | 2025/2026 etc. |
| `semesters` | First/Second/etc. — belongs to an academic year. |

### People

| Table | Purpose |
|-------|---------|
| `students` | One row per enrolled student. Links to `users.id` (auth) and `programs.id` (academic track). Unique per institution: `student_id` (matric). Carries `nsi_number` (national ID, **do not alter format — see CLAUDE.md**). |
| `enrollments` | Per-course-per-semester enrolment. |
| `grades` | Marks scored per enrolment. Audited. |
| `cgpa_records` | Semester GPA + cumulative GPA snapshots. |
| `staff` | Teaching and non-teaching staff. Links to `users`. |
| `designations` | Job titles (Lecturer, Senior Lecturer, Professor, …). |
| `teaching_assignments` | Which staff teaches which course_section. |
| `leave_requests` | Staff leave workflow. |

### Finance

| Table | Purpose |
|-------|---------|
| `fee_categories` | Tuition, hostel, library fine, etc. |
| `fee_structures` | Amount per category per program per level. |
| `invoices` | Per-student, per-semester invoice header. Audited. |
| `invoice_items` | Line items — links to `fee_categories`. |
| `payments` | Receipt of money. Audited. |
| `peeappay_transactions` | Raw callback records from PeeapPay gateway. |
| `scholarships` | Scholarship programs offered. |
| `student_scholarships` | Award pivot. |
| `payroll_runs` + `payroll_items` | Staff salary processing. |

### Attendance & exams

| Table | Purpose |
|-------|---------|
| `attendances` | Student attendance per course_section per date. |
| `staff_attendances` | Staff clock-in/out. |
| `exams` | Exam definitions. |
| `exam_types` | Mid-term, final, quiz, etc. |
| `exam_schedules` | When and where each exam is held. |
| `grade_scales` | Grade → grade_point mapping (A = 4.0, B+ = 3.5, …). Institution-specific. |
| `result_publications` | Publishing workflow for semester results. |

### Library

| Table | Purpose |
|-------|---------|
| `books` | Titles in the library collection. |
| `book_categories` | Subject categories. |
| `book_issues` | Check-out/return records. |

### Communication

| Table | Purpose |
|-------|---------|
| `messages` | Direct messages between users. |
| `notices` | Announcements (audience: all / students / staff / parents). |
| `notifications` | Laravel default `notifications` table for in-app push. |

### Admissions

| Table | Purpose |
|-------|---------|
| `admission_settings` | Per-institution admission config. |
| `admissions` | Applications submitted via `/apply`. Status: pending / approved / rejected. |
| `nsi_verifications` | Records of NSI lookups performed. |
| `id_cards` | Generated ID card metadata and designs. |
| `frontend_settings` | Public website customisation (hero, about, colors, etc.) per tenant. |

### Framework / infrastructure

Laravel defaults: `sessions` (when `SESSION_DRIVER=database`),
`jobs`, `job_batches`, `failed_jobs`, `cache`, `cache_locks`,
`password_reset_tokens`.

---

## Multi-tenancy: how rows are scoped

**Single-database, tenant-scoped** — there is ONE MySQL database. Every
tenant-owned row carries an `institution_id` column, and the
`App\Modules\Core\Traits\BelongsToInstitution` trait adds a global scope
to Eloquent models so queries automatically filter by the current
request's institution.

The platform-level institution lives at `institutions.id = 1`; it owns
no students/staff/etc. but is the context for HEMIS and platform-admin
operations on the root domain `college.edu.sl`.

Concrete example of how a row is scoped end-to-end:

1. User hits `njala.college.edu.sl/students`
2. `TenantMiddleware` parses the subdomain → looks up `institutions
   WHERE domain='njala'` → binds `app('institution')` to that row
3. `StudentController@index` calls `Student::all()`
4. `BelongsToInstitution` global scope appends `WHERE
   institution_id = <njala id>` to the query
5. Only njala's students come back, even though unimak's students live
   in the same table

This means:
- **Cross-tenant data leakage is prevented by convention** — as long as
  every tenant-owned model uses the trait, no extra guards are needed
- **HEMIS bypasses this scope** by querying models directly with an
  explicit `institution_id != 1` filter (it lives on the platform
  institution)
- **Adding a new tenant** is as simple as inserting one row into
  `institutions` and its admin user — no new schema, no new DB, no
  schema migrations

The alternative (one database per tenant) was considered and rejected
because it multiplies deployment complexity, breaks the centralising
goal of HEMIS, and doesn't meaningfully improve security given the
scope trait + audit logging.

---

## What `php artisan migrate --seed` creates

`DatabaseSeeder` calls `OpenCollegeSeeder`, which creates a realistic
demo dataset. The seeder is **idempotent-hostile** — it uses
`create()` not `firstOrCreate()`, so running it twice on the same DB
will fail with unique-key violations. Run only on a fresh empty DB.

### Platform (institution id = 1)

- **1 institution:** "OpenCollege Platform" (code `OC`, domain
  `platform`, type `platform`)
- **1 role:** `super_admin`
- **1 user:** `admin@opencollege.test` / `password` (hashed)

### Demo college (institution id = 2)

- **1 institution:** "College of Sierra Leone" (code `CSL`, domain
  `csl`, type `college`)
- **6 roles:** admin, registrar, lecturer, student, librarian,
  accountant
- **1 user:** `admin@csl.opencollege.test` / `password` — attached to
  the admin role
- **1 academic year:** 2025/2026
- **2 semesters:** First Semester, Second Semester
- **6 designations:** Professor, Associate Professor, Senior Lecturer,
  Lecturer, Assistant Lecturer, Tutor
- **3 faculties, 6 departments, ~12 programs** — a realistic
  faculty-dept-program tree covering Science, Engineering, and Humanities
- **Demo lecturers:** ~4–6 staff records attached to programs
- **Demo students:** ~20 enrolled across programs, with realistic names,
  genders, dates of birth, and matric numbers

Everything is attached to institution id=2 so it does not leak into
HEMIS aggregate counts for real-world institutions.

---

## Setting up a brand-new institution (post-install)

When a real institution onboards (not the demo), use the platform admin
flow instead of the seeder:

1. Log in at `college.edu.sl/login` as the platform admin
2. Go to **Manage Colleges → Register new college**
3. Fill the form (name, code, domain slug, type, primary admin email)
4. The system creates the institution, default roles, admin user with a
   temporary password (shown once on screen), and the current academic
   year
5. New college's portal is immediately live at
   `https://<slug>.college.edu.sl`

Under the hood, this runs the same logic that `OpenCollegeSeeder` uses
for the demo college, but scoped to the admin-supplied values.

---

## Backups

The deployment pipeline does **not** take backups automatically. This
is the operator's responsibility. Recommended:

### Nightly mysqldump to off-server storage

```cron
15 2 * * * /usr/bin/mysqldump -u backupuser -p'$ECRET' \
  --single-transaction --quick --no-tablespaces \
  --routines --triggers \
  opencollege | gzip > /home/backups/opencollege-$(date +\%Y\%m\%d).sql.gz
```

Rotate locally (keep 30 days), mirror to S3/Backblaze for 1+ year
retention. Test restore quarterly — an untested backup is not a backup.

### Before any destructive change

```bash
mysqldump -u root -p opencollege > pre-change-$(date +%Y%m%d-%H%M%S).sql
```

Do this before:
- Running new migrations on production
- Deleting a tenant (soft-delete still recoverable, but hard-delete
  removes children)
- Bulk operations (`artisan tinker` scripts that update many rows)

### Restoring a backup

```bash
# Restore full database (destroys current contents)
gunzip < opencollege-20260417.sql.gz | mysql -u root -p opencollege

# Restore a single table (e.g. after accidental drop)
gunzip < opencollege-20260417.sql.gz \
  | sed -n '/^-- Table structure for table `students`/,/^-- Table structure for table `/p' \
  | mysql -u root -p opencollege
```

---

## Common maintenance queries

Find orphaned records:

```sql
-- Users with no institution
SELECT COUNT(*) FROM users WHERE current_institution_id IS NULL;

-- Students whose user was deleted
SELECT s.id, s.student_id, s.user_id
FROM students s
LEFT JOIN users u ON u.id = s.user_id
WHERE u.id IS NULL;

-- Programs without a department
SELECT COUNT(*) FROM programs p
LEFT JOIN departments d ON d.id = p.department_id
WHERE d.id IS NULL;
```

Per-institution row counts:

```sql
SELECT i.name, i.domain,
    (SELECT COUNT(*) FROM students WHERE institution_id = i.id) AS students,
    (SELECT COUNT(*) FROM staff WHERE institution_id = i.id)    AS staff,
    (SELECT COUNT(*) FROM programs WHERE institution_id = i.id) AS programs,
    (SELECT COUNT(*) FROM invoices WHERE institution_id = i.id) AS invoices
FROM institutions i
WHERE i.id > 1
ORDER BY students DESC;
```

Audit log for a specific student:

```sql
SELECT al.created_at, al.action, u.name AS actor, al.ip_address,
       al.before, al.after
FROM audit_logs al
LEFT JOIN users u ON u.id = al.user_id
WHERE al.model_type = 'App\\Modules\\Student\\Models\\Student'
  AND al.model_id = 12345
ORDER BY al.created_at DESC;
```

---

## Troubleshooting

**"Base table or view not found"** — a migration that introduces the
table hasn't been run. Run `php artisan migrate` and check the
`migrations` table to see which files have been applied.

**"Column not found" after a pull** — someone added a migration you
haven't run. `php artisan migrate --force` on production (or
`php artisan migrate` in dev).

**Migration fails halfway** — Laravel doesn't roll back transaction-wise
unless the migration explicitly uses `DB::transaction`. You may end up
in a partial state. Fix: manually SQL the problematic change, then add
a row to the `migrations` table to mark it as applied, OR restore from
backup and redeploy.

**`Row size too large`** — usually triggered by creating tables with
too many `TEXT`/`VARCHAR(255)` columns on MySQL. Fix by splitting the
table or using `ROW_FORMAT=DYNAMIC`.

**Slow cross-tenant HEMIS queries** — at scale, add indexes:
```sql
CREATE INDEX idx_students_institution_id ON students(institution_id);
CREATE INDEX idx_students_nsi             ON students(nsi_number);
CREATE INDEX idx_audit_logs_model         ON audit_logs(model_type, model_id);
CREATE INDEX idx_audit_logs_created       ON audit_logs(created_at);
```
(The safety-net migration already adds some of these; check
`migrations/2026_04_17_000001_add_safety_net_tables.php`.)
