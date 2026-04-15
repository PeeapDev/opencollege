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

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            // Redirect students to student portal
            if ($user->hasRole('student')) {
                return redirect()->intended(route('student.portal'));
            }
            return redirect()->intended(route('dashboard'));
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
