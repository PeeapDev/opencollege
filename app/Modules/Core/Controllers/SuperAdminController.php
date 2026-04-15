<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Institution;
use App\Modules\Student\Models\Student;
use App\Modules\Staff\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_colleges' => Institution::where('id', '>', 1)->count(),
            'active_colleges' => Institution::where('id', '>', 1)->where('active', true)->count(),
            'total_students' => Student::count(),
            'total_staff' => Staff::count(),
            'total_users' => User::count(),
            'pending_colleges' => Institution::where('accreditation_status', 'pending')->where('id', '>', 1)->count(),
        ];

        $colleges = Institution::where('id', '>', 1)
            ->withCount(['students' => function ($q) { $q->where('status', 'active'); }])
            ->withCount(['staffMembers' => function ($q) { $q->where('status', 'active'); }])
            ->latest()
            ->take(10)
            ->get();

        $recentColleges = Institution::where('id', '>', 1)->latest()->take(5)->get();

        // Monthly registrations for chart (last 12 months)
        $monthlyData = Institution::where('id', '>', 1)
            ->where('created_at', '>=', now()->subMonths(12))
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m'))
            ->map(fn($group) => $group->count())
            ->sortKeys()
            ->toArray();

        return view('core::superadmin.dashboard', compact('stats', 'colleges', 'recentColleges', 'monthlyData'));
    }

    public function colleges()
    {
        $colleges = Institution::where('id', '>', 1)
            ->withCount(['students' => function ($q) { $q->where('status', 'active'); }])
            ->withCount(['staffMembers' => function ($q) { $q->where('status', 'active'); }])
            ->latest()
            ->paginate(20);

        return view('core::superadmin.colleges', compact('colleges'));
    }

    public function createCollege()
    {
        return view('core::superadmin.college_create');
    }

    public function storeCollege(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:institutions,code',
            'domain' => 'required|string|max:100|unique:institutions,domain|alpha_num',
            'email' => 'required|email',
            'phone' => 'required|string|max:30',
            'type' => 'required|in:college,polytechnic,university',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
        ]);

        $tempAdminPassword = \Illuminate\Support\Str::random(16);
        DB::transaction(function () use ($request, $tempAdminPassword) {
            $institution = Institution::create([
                'name' => $request->name,
                'code' => $request->code,
                'domain' => strtolower($request->domain),
                'type' => $request->type,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country ?? 'Sierra Leone',
                'description' => $request->description,
                'registration_number' => $request->registration_number,
                'accreditation_status' => 'pending',
                'plan' => 'free',
            ]);

            // Create admin user for the college
            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($tempAdminPassword),
                'current_institution_id' => $institution->id,
            ]);

            // Create admin role for this institution
            $role = \App\Modules\Settings\Models\Role::create([
                'institution_id' => $institution->id,
                'name' => 'admin',
                'display_name' => 'College Admin',
                'is_system' => true,
            ]);

            // Create default roles
            foreach (['registrar' => 'Registrar', 'lecturer' => 'Lecturer', 'student' => 'Student', 'librarian' => 'Librarian', 'accountant' => 'Accountant'] as $name => $display) {
                \App\Modules\Settings\Models\Role::create([
                    'institution_id' => $institution->id,
                    'name' => $name,
                    'display_name' => $display,
                    'is_system' => true,
                ]);
            }

            $admin->roles()->attach($role->id, ['institution_id' => $institution->id]);

            // Create default academic year
            \App\Modules\Academic\Models\AcademicYear::create([
                'institution_id' => $institution->id,
                'title' => '2025/2026',
                'start_date' => '2025-09-01',
                'end_date' => '2026-08-31',
                'is_current' => true,
            ]);
        });

        return redirect()->route('superadmin.colleges')->with('success', "College \'{$request->name}\' registered. Temporary admin password (shown once): {$tempAdminPassword}");
    }

    public function toggleCollege(Institution $institution)
    {
        $institution->update(['active' => !$institution->active]);
        return back()->with('success', $institution->name . ($institution->active ? ' activated.' : ' deactivated.'));
    }

    public function destroyCollege(Request $request, Institution $institution)
    {
        if ($institution->id <= 1) {
            return back()->with('error', 'Cannot delete the platform institution.');
        }
        // Require the operator to type the college name exactly as a safety check.
        if ($request->input('confirm_name') !== $institution->name) {
            return back()->with('error', 'College name confirmation did not match. Deletion aborted.');
        }
        // Soft-delete if the model supports it; otherwise fall back to hard delete.
        if (in_array('Illuminate\\Database\\Eloquent\\SoftDeletes', class_uses_recursive($institution))) {
            $institution->delete();
        } else {
            \Illuminate\Support\Facades\Log::warning('Hard-deleting college', ['id' => $institution->id, 'name' => $institution->name, 'by' => auth()->id()]);
            $institution->delete();
        }
        return redirect()->route('superadmin.colleges')->with('success', 'College deleted.');
    }
}
