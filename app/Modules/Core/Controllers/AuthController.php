<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Modules\Student\Models\Student;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('core::auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required',
        ]);

        $identifier = trim($request->identifier);
        $remember = $request->boolean('remember');

        // Detect identifier type and find user
        $user = null;
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
        } elseif (preg_match('/^\+?\d{6,}$/', $identifier)) {
            $user = User::where('phone', $identifier)->first();
        } else {
            // Try username first
            $user = User::where('username', $identifier)->first();
            // Then try NSI number (look up student)
            if (!$user) {
                $student = Student::where('nsi_number', $identifier)->first();
                if ($student) {
                    $user = $student->user;
                }
            }
            // Then try student_id (matric number)
            if (!$user) {
                $student = Student::where('student_id', $identifier)->first();
                if ($student) {
                    $user = $student->user;
                }
            }
        }

        // Account lockout check (DPG Criterion 9)
        if ($user && $user->locked_until && now()->lt($user->locked_until)) {
            $mins = now()->diffInMinutes($user->locked_until) + 1;
            \Log::warning('Login blocked: account locked', ['user_id' => $user->id, 'ip' => $request->ip()]);
            return back()->withErrors(['identifier' => "Account is temporarily locked. Try again in {$mins} minute(s)."])->onlyInput('identifier');
        }

        if ($user && Hash::check($request->password, $user->password)) {
            // Successful login — reset counters, record metadata
            $user->failed_login_attempts = 0;
            $user->locked_until = null;
            $user->last_login_at = now();
            $user->last_login_ip = $request->ip();
            $user->saveQuietly();

            Auth::login($user, $remember);
            $request->session()->regenerate();

            // Redirect students to student portal
            if ($user->hasRole('student')) {
                return redirect()->intended(route('student.portal'));
            }
            return redirect()->intended(route('dashboard'));
        }

        // Failed login — track attempts on the user record
        if ($user) {
            $user->failed_login_attempts = (int) $user->failed_login_attempts + 1;
            if ($user->failed_login_attempts >= 10) {
                $user->locked_until = now()->addMinutes(15);
                \Log::warning('Account locked after failed attempts', ['user_id' => $user->id, 'ip' => $request->ip()]);
            }
            $user->saveQuietly();
        }

        return back()->withErrors(['identifier' => 'Invalid credentials. Check your email, phone, NSI, or matric number and password.'])->onlyInput('identifier');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
