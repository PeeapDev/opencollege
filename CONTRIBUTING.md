# Contributing to OpenCollege

Thank you for your interest in contributing to OpenCollege! This document provides guidelines for contributing to the project.

## Code of Conduct

By participating in this project, you agree to maintain a respectful and inclusive environment for everyone.

## How to Contribute

### Reporting Bugs

1. Check if the bug has already been reported in the Issues section
2. Create a new issue with a clear title and description
3. Include steps to reproduce, expected behavior, and actual behavior
4. Add screenshots if applicable
5. Include your environment details (PHP version, MySQL version, OS)

### Suggesting Features

1. Open a new issue with the `feature-request` label
2. Describe the feature and its use case
3. Explain how it benefits the college management workflow

### Submitting Code

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Make your changes following the coding standards below
4. Write or update tests as needed
5. Commit with clear messages: `git commit -m "Add: brief description"`
6. Push to your fork: `git push origin feature/your-feature-name`
7. Open a Pull Request against the `main` branch

## Coding Standards

### PHP / Laravel

- Follow PSR-12 coding style
- Use strict typing where possible
- Keep controllers thin — move business logic to services
- Use Eloquent relationships instead of raw queries where practical
- All database queries must be scoped by `institution_id` for multi-tenancy

### Module Development

When creating a new module:

1. Create directory: `app/Modules/YourModule/`
2. Add `module.json` manifest:
   ```json
   {
       "name": "YourModule",
       "alias": "yourmodule",
       "description": "Description of the module",
       "version": "1.0.0",
       "priority": 5,
       "providers": [],
       "dependencies": ["Core"],
       "keywords": ["keyword1", "keyword2"],
       "active": true
   }
   ```
3. Create standard directories: `Controllers/`, `Models/`, `Routes/`, `Views/`, `Migrations/`
4. Routes go in `Routes/web.php` and/or `Routes/api.php`
5. Views use the module alias: `return view('yourmodule::viewname')`
6. Migrations should use `Schema::hasTable()` checks for safety

### Blade Templates

- Extend `core::layouts.app` for admin pages
- Use Tailwind CSS utility classes (loaded via CDN)
- Use Font Awesome for icons
- Use Alpine.js for client-side interactivity
- Keep templates clean and readable

### Database

- Always scope queries by `institution_id` for multi-tenancy
- Use migrations with `hasTable()` guards
- Add proper indexes for frequently queried columns
- Use foreign key constraints where appropriate

## Commit Message Convention

```
Add: new feature description
Fix: bug description
Update: what was changed
Remove: what was removed
Docs: documentation changes
Refactor: code refactoring
```

## Development Setup

```bash
git clone https://github.com/PeeapDev/opencollege.git
cd opencollege
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Questions?

Open an issue or reach out to the maintainers at [PeeapDev](https://github.com/PeeapDev).

---

Thank you for helping make education technology accessible to all!
