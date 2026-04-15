<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CollegeRegistrationController extends Controller
{
    public function showForm()
    {
        return view('core::auth.register_college');
    }

    public function register(Request $request)
    {
        $request->validate([
            'college_name' => 'required|string|max:255',
            'college_type' => 'required|in:college,polytechnic,university',
            'domain' => 'required|string|max:100|unique:institutions,domain|alpha_num',
            'email' => 'required|email|unique:institutions,email',
            'phone' => 'required|string|max:30',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ], [
            'domain.unique' => 'This subdomain is already taken.',
            'domain.alpha_num' => 'Subdomain may only contain letters and numbers.',
        ]);

        DB::transaction(function () use ($request) {
            $code = strtoupper(substr($request->domain, 0, 10));

            $institution = Institution::create([
                'name' => $request->college_name,
                'code' => $code,
                'domain' => strtolower($request->domain),
                'type' => $request->college_type,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'country' => 'Sierra Leone',
                'accreditation_status' => 'pending',
                'plan' => 'free',
                'subscription_start' => now(),
                'subscription_end' => now()->addYear(),
            ]);

            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'current_institution_id' => $institution->id,
            ]);

            $role = \App\Modules\Settings\Models\Role::create([
                'institution_id' => $institution->id,
                'name' => 'admin',
                'display_name' => 'College Admin',
                'is_system' => true,
            ]);

            foreach (['registrar' => 'Registrar', 'lecturer' => 'Lecturer', 'student' => 'Student', 'librarian' => 'Librarian', 'accountant' => 'Accountant'] as $name => $display) {
                \App\Modules\Settings\Models\Role::create([
                    'institution_id' => $institution->id,
                    'name' => $name,
                    'display_name' => $display,
                    'is_system' => true,
                ]);
            }

            $admin->roles()->attach($role->id, ['institution_id' => $institution->id]);

            \App\Modules\Academic\Models\AcademicYear::create([
                'institution_id' => $institution->id,
                'title' => date('Y') . '/' . (date('Y') + 1),
                'start_date' => date('Y') . '-09-01',
                'end_date' => (date('Y') + 1) . '-08-31',
                'is_current' => true,
            ]);
        });

        $subdomain = strtolower($request->domain);
        $baseUrl = preg_replace('#^https?://#', '', rtrim(config('app.url'), '/'));

        return redirect()->route('login')->with('success', "College registered! Access your portal at: {$subdomain}.{$baseUrl}");
    }
}
