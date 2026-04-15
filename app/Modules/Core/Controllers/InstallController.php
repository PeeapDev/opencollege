<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

/**
 * Web-based installer — Codecanyon-style wizard.
 *
 * Disabled automatically once installation finishes (lock file at
 * storage/installed). To reinstall, delete the lock file and the .env.
 */
class InstallController extends Controller
{
    private const LOCK = 'installed';

    private array $requiredExtensions = [
        'bcmath', 'ctype', 'curl', 'dom', 'fileinfo', 'gd', 'intl', 'json',
        'mbstring', 'openssl', 'pcre', 'pdo', 'pdo_mysql', 'tokenizer', 'xml', 'zip',
    ];

    // ------------------------------------------------------------------
    // Step 1 — Welcome & requirements
    // ------------------------------------------------------------------
    public function welcome()
    {
        return view('install.welcome');
    }

    public function requirements()
    {
        $checks = [
            'PHP >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
        ];
        foreach ($this->requiredExtensions as $ext) {
            $checks["ext: $ext"] = extension_loaded($ext);
        }
        $checks['storage/ writable']        = is_writable(storage_path());
        $checks['bootstrap/cache writable'] = is_writable(base_path('bootstrap/cache'));
        $checks['.env writable or absent']  = !file_exists(base_path('.env')) || is_writable(base_path('.env'));

        $ok = !in_array(false, $checks, true);

        return view('install.requirements', compact('checks', 'ok'));
    }

    // ------------------------------------------------------------------
    // Step 2 — Database
    // ------------------------------------------------------------------
    public function dbForm()
    {
        return view('install.database', ['data' => session('install.db', [])]);
    }

    public function dbSubmit(Request $request)
    {
        $data = $request->validate([
            'db_host'     => 'required|string',
            'db_port'     => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            config([
                'database.connections.install_test' => [
                    'driver'   => 'mysql',
                    'host'     => $data['db_host'],
                    'port'     => $data['db_port'],
                    'database' => $data['db_database'],
                    'username' => $data['db_username'],
                    'password' => $data['db_password'] ?? '',
                    'charset'  => 'utf8mb4',
                    'collation'=> 'utf8mb4_unicode_ci',
                ],
            ]);
            DB::connection('install_test')->getPdo();
        } catch (Throwable $e) {
            return back()->withInput()->withErrors([
                'db' => 'Could not connect: '.$e->getMessage(),
            ]);
        }

        session(['install.db' => $data]);
        return redirect()->route('install.site');
    }

    // ------------------------------------------------------------------
    // Step 3 — Site configuration
    // ------------------------------------------------------------------
    public function siteForm()
    {
        return view('install.site', ['data' => session('install.site', [])]);
    }

    public function siteSubmit(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'required|string|max:120',
            'app_url'  => 'required|url',
            'timezone' => 'required|string',
            'mail_from'=> 'nullable|email',
        ]);
        session(['install.site' => $data]);
        return redirect()->route('install.admin');
    }

    // ------------------------------------------------------------------
    // Step 4 — Super admin account
    // ------------------------------------------------------------------
    public function adminForm()
    {
        return view('install.admin');
    }

    public function adminSubmit(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:120',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'seed_demo'=> 'nullable|boolean',
        ]);
        session(['install.admin' => $data]);
        return redirect()->route('install.finalize');
    }

    // ------------------------------------------------------------------
    // Step 5 — Finalize (write .env, migrate, seed, create admin)
    // ------------------------------------------------------------------
    public function finalize()
    {
        return view('install.finalize');
    }

    public function run(Request $request)
    {
        $db    = session('install.db');
        $site  = session('install.site');
        $admin = session('install.admin');

        if (!$db || !$site || !$admin) {
            return redirect()->route('install.welcome')->with('error', 'Session expired — please start again.');
        }

        try {
            $this->writeEnv($db, $site);

            // Reload config to pick up the new .env
            Artisan::call('config:clear');

            // Key + migrate + optional seed
            Artisan::call('key:generate', ['--force' => true]);
            Artisan::call('migrate', ['--force' => true]);

            if (!empty($admin['seed_demo'])) {
                Artisan::call('db:seed', ['--force' => true]);
            }

            // Create super-admin user
            $userClass = '\\App\\Models\\User';
            if (class_exists($userClass)) {
                $userClass::updateOrCreate(
                    ['email' => $admin['email']],
                    [
                        'name'     => $admin['name'],
                        'password' => Hash::make($admin['password']),
                    ]
                );
            }

            // Link storage (idempotent)
            try { Artisan::call('storage:link'); } catch (Throwable $e) {}

            // Lock file — blocks future re-runs
            File::put(storage_path(self::LOCK), now()->toIso8601String());

            // Clean session
            session()->forget(['install.db', 'install.site', 'install.admin']);

            return response()->json([
                'ok'  => true,
                'msg' => 'Installation complete.',
                'redirect' => $site['app_url'].'/login',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Install failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function done()
    {
        return view('install.done', [
            'loginUrl' => rtrim(config('app.url'), '/').'/login',
        ]);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------
    private function writeEnv(array $db, array $site): void
    {
        $envPath = base_path('.env');
        $examplePath = base_path('.env.example');

        $env = file_exists($envPath)
            ? file_get_contents($envPath)
            : (file_exists($examplePath) ? file_get_contents($examplePath) : '');

        $pairs = [
            'APP_NAME'     => '"'.str_replace('"', '', $site['app_name']).'"',
            'APP_ENV'      => 'production',
            'APP_DEBUG'    => 'false',
            'APP_URL'      => $site['app_url'],
            'APP_TIMEZONE' => $site['timezone'],
            'DB_CONNECTION'=> 'mysql',
            'DB_HOST'      => $db['db_host'],
            'DB_PORT'      => $db['db_port'],
            'DB_DATABASE'  => $db['db_database'],
            'DB_USERNAME'  => $db['db_username'],
            'DB_PASSWORD'  => '"'.str_replace('"', '', $db['db_password'] ?? '').'"',
            'MAIL_FROM_ADDRESS' => $site['mail_from'] ?? 'noreply@'.parse_url($site['app_url'], PHP_URL_HOST),
            'APP_KEY'      => 'base64:'.base64_encode(random_bytes(32)),
        ];

        foreach ($pairs as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $line = "{$key}={$value}";
            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, $line, $env);
            } else {
                $env .= "\n{$line}";
            }
        }

        file_put_contents($envPath, $env);
    }
}
