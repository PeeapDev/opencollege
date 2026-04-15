<?php

namespace App\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    protected array $modules = [
        'Core',
        'Academic',
        'Student',
        'Staff',
        'Finance',
        'Attendance',
        'Exam',
        'Library',
        'Communication',
        'Settings',
        'HumanResource',
    ];

    public function boot(): void
    {
        $activeModules = $this->getActiveModules();

        foreach ($activeModules as $module) {
            $this->loadModuleRoutes($module);
            $this->loadModuleViews($module);
            $this->loadModuleMigrations($module);
        }
    }

    protected function getActiveModules(): array
    {
        $discovered = [];
        $modulesPath = app_path('Modules');

        foreach (glob($modulesPath . '/*/module.json') as $manifestFile) {
            $manifest = json_decode(file_get_contents($manifestFile), true);
            if (($manifest['active'] ?? false) && isset($manifest['name'])) {
                $discovered[$manifest['name']] = $manifest['priority'] ?? 99;
            }
        }

        // Merge: discovered modules + hardcoded fallback (in case module.json missing)
        foreach ($this->modules as $module) {
            if (!isset($discovered[$module])) {
                $discovered[$module] = 99;
            }
        }

        asort($discovered);
        return array_keys($discovered);
    }

    protected function loadModuleRoutes(string $module): void
    {
        $webRoutes = app_path("Modules/{$module}/Routes/web.php");
        $apiRoutes = app_path("Modules/{$module}/Routes/api.php");

        if (file_exists($webRoutes)) {
            Route::middleware('web')
                ->group($webRoutes);
        }

        if (file_exists($apiRoutes)) {
            Route::prefix('api/v1')
                ->middleware('api')
                ->group($apiRoutes);
        }
    }

    protected function loadModuleViews(string $module): void
    {
        $viewPath = app_path("Modules/{$module}/Views");
        if (is_dir($viewPath)) {
            $alias = strtolower($module);
            // Map HumanResource -> hr for shorter view references
            $aliasMap = ['humanresource' => 'hr'];
            $alias = $aliasMap[$alias] ?? $alias;
            $this->loadViewsFrom($viewPath, $alias);
        }
    }

    protected function loadModuleMigrations(string $module): void
    {
        $migrationPath = app_path("Modules/{$module}/Migrations");
        if (is_dir($migrationPath)) {
            $this->loadMigrationsFrom($migrationPath);
        }
    }
}
