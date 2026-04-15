# OpenCollege Module Documentation

## Module System Overview

OpenCollege uses a modular architecture where each functional area is an independent module. Modules are auto-discovered by the `ModuleServiceProvider` which scans for `module.json` manifests.

## Module Structure

Each module follows this standard structure:

```
app/Modules/ModuleName/
├── module.json          # Module manifest (required)
├── Controllers/         # HTTP controllers
├── Models/              # Eloquent models
├── Routes/
│   ├── web.php          # Web routes
│   └── api.php          # API routes (optional)
├── Views/               # Blade templates
├── Migrations/          # Database migrations
└── Services/            # Business logic services (optional)
```

## Module Manifest (module.json)

Every module must have a `module.json` file:

```json
{
    "name": "ModuleName",
    "alias": "modulename",
    "description": "What this module does",
    "version": "1.0.0",
    "priority": 5,
    "providers": [],
    "dependencies": ["Core"],
    "keywords": ["keyword1", "keyword2"],
    "active": true
}
```

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Module directory name (PascalCase) |
| `alias` | string | Short name used for view references |
| `description` | string | Human-readable description |
| `version` | string | Semantic version |
| `priority` | int | Load order (0 = first, higher = later) |
| `providers` | array | Additional service providers to register |
| `dependencies` | array | Required modules that must load first |
| `keywords` | array | Searchable tags |
| `active` | bool | Whether module is enabled |

## Module Registry

### Core (Priority 0)
- **Alias:** `core`
- **Dependencies:** None
- **Provides:** Authentication, multi-tenancy, dashboard, NSI verification, super admin panel, frontend pages, layout system
- **Key Controllers:** `AuthController`, `DashboardController`, `NsiVerificationController`, `SuperAdminController`, `FrontendController`, `CollegeRegistrationController`

### Academic (Priority 1)
- **Alias:** `academic`
- **Dependencies:** Core
- **Provides:** Faculty, department, program, and course management
- **Key Models:** `Faculty`, `Department`, `Program`, `Course`

### Student (Priority 2)
- **Alias:** `student`
- **Dependencies:** Core, Academic
- **Provides:** Student enrollment, admissions, student portal, ID cards with QR codes
- **Key Controllers:** `StudentController`, `AdmissionController`, `StudentPortalController`

### Staff (Priority 2)
- **Alias:** `staff`
- **Dependencies:** Core, Academic
- **Provides:** Staff records, designations, teaching assignments
- **Key Models:** `Staff`, `Designation`

### Finance (Priority 3)
- **Alias:** `finance`
- **Dependencies:** Core, Student
- **Provides:** Invoices, payments, fee categories, scholarships, PeeapPay online payments
- **Key Services:** `PeeapPayService` — handles payment initialization, verification, and webhooks
- **Key Controllers:** `InvoiceController`, `PaymentController`, `PeeapPayController`

### Exam (Priority 3)
- **Alias:** `exam`
- **Dependencies:** Core, Academic, Student
- **Provides:** Exam scheduling, grading, result stores, result publications, exam board

### HumanResource (Priority 3)
- **Alias:** `hr`
- **Dependencies:** Core, Staff, Finance
- **Provides:** Leave management, payroll processing, staff directory, HR dashboard
- **Key Controllers:** `HrController`
- **Tables:** `payroll_runs`, `payroll_items`

### Attendance (Priority 3)
- **Alias:** `attendance`
- **Dependencies:** Core, Student, Staff
- **Provides:** Student and staff attendance tracking, reports, analytics

### Communication (Priority 4)
- **Alias:** `communication`
- **Dependencies:** Core
- **Provides:** Notices, internal messaging, announcements

### Library (Priority 4)
- **Alias:** `library`
- **Dependencies:** Core, Student
- **Provides:** Book catalog, categories, issue/return tracking

### Settings (Priority 0)
- **Alias:** `settings`
- **Dependencies:** Core
- **Provides:** Institution configuration, roles, permissions, system preferences

## Creating a New Module

1. Create directory: `app/Modules/YourModule/`
2. Add `module.json` manifest (see format above)
3. Create `Controllers/`, `Routes/web.php`, `Views/`, `Migrations/`
4. Views are referenced as `yourmodule::viewname` (using the alias from module.json)
5. The module is automatically discovered and loaded — no manual registration needed

## Multi-Tenancy in Modules

All database queries in modules must be scoped by `institution_id`:

```php
$data = Model::where('institution_id', auth()->user()->current_institution_id)->get();
```

The `TenantMiddleware` handles institution identification from subdomains.

## NSI Verification Flow

1. Admin enters NSI number in OpenCollege
2. `NsiVerificationController` calls SDSL API at `gov.school.edu.sl`
3. SDSL returns full academic history (all classes, exam results, milestones)
4. Results stored in `nsi_verifications` table with full API response
5. Detail view shows complete academic journey with color-coded exam milestones

## PeeapPay Payment Flow

1. Admin/student clicks "Pay Online" on an invoice
2. `PeeapPayController` initializes transaction with PeeapPay API
3. User is redirected to PeeapPay checkout
4. On completion, callback URL verifies the transaction
5. Webhook provides real-time confirmation
6. Payment record is created and invoice balance updated automatically
