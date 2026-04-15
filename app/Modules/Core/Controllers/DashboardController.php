<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Student\Models\Student;
use App\Modules\Staff\Models\Staff;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\CourseSection;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Faculty;
use App\Modules\Academic\Models\Semester;
use App\Modules\Finance\Models\Payment;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Attendance\Models\Attendance;
use App\Modules\Settings\Models\Institution;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Super admin sees the super admin dashboard
        if ($user->hasRole('super_admin') && $user->current_institution_id == 1) {
            return app(SuperAdminController::class)->dashboard();
        }

        $institutionId = $user->current_institution_id;
        $institution = Institution::find($institutionId);

        // Core stats
        $stats = [
            'total_students' => Student::where('students.institution_id', $institutionId)->where('status', 'active')->count(),
            'total_staff' => Staff::where('staff.institution_id', $institutionId)->where('status', 'active')->count(),
            'total_programs' => Program::where('programs.institution_id', $institutionId)->where('active', true)->count(),
            'total_courses' => Course::where('courses.institution_id', $institutionId)->where('active', true)->count(),
            'total_departments' => Department::where('departments.institution_id', $institutionId)->where('active', true)->count(),
            'total_faculties' => Faculty::where('faculties.institution_id', $institutionId)->where('active', true)->count(),
            'total_revenue' => Payment::where('payments.institution_id', $institutionId)->sum('amount'),
            'pending_invoices' => Invoice::where('invoices.institution_id', $institutionId)->whereIn('status', ['unpaid', 'partial', 'overdue'])->count(),
            'paid_invoices' => Invoice::where('invoices.institution_id', $institutionId)->where('status', 'paid')->count(),
            'total_invoiced' => Invoice::where('invoices.institution_id', $institutionId)->sum('total_amount'),
            'male_students' => Student::where('students.institution_id', $institutionId)->where('status', 'active')->where('gender', 'male')->count(),
            'female_students' => Student::where('students.institution_id', $institutionId)->where('status', 'active')->where('gender', 'female')->count(),
        ];

        // Students by program (for chart)
        $studentsByProgram = Student::where('students.institution_id', $institutionId)
            ->where('students.status', 'active')
            ->join('programs', 'students.program_id', '=', 'programs.id')
            ->selectRaw('programs.name as program_name, COUNT(*) as count')
            ->groupBy('programs.name')
            ->orderByDesc('count')
            ->take(8)
            ->pluck('count', 'program_name')
            ->toArray();

        // Monthly enrollment trend (last 12 months)
        $enrollmentTrend = Student::where('students.institution_id', $institutionId)
            ->where('students.created_at', '>=', now()->subMonths(12))
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, COUNT(*) as count")
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %Y')")
            ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m')")
            ->pluck('count', 'month')
            ->toArray();

        // Revenue trend (last 6 months)
        $revenueTrend = Payment::where('payments.institution_id', $institutionId)
            ->where('payments.created_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%b') as month, SUM(amount) as total")
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b')")
            ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m')")
            ->pluck('total', 'month')
            ->toArray();

        // Students by status
        $studentsByStatus = Student::where('students.institution_id', $institutionId)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent students
        $recentStudents = Student::where('students.institution_id', $institutionId)
            ->with('user', 'program')
            ->latest()
            ->take(5)
            ->get();

        // Recent payments
        $recentPayments = Payment::where('payments.institution_id', $institutionId)
            ->with('student.user', 'invoice')
            ->latest()
            ->take(5)
            ->get();

        // Staff by department
        $staffByDept = Staff::where('staff.institution_id', $institutionId)
            ->where('staff.status', 'active')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as dept, COUNT(*) as count')
            ->groupBy('departments.name')
            ->pluck('count', 'dept')
            ->toArray();

        return view('core::dashboard', compact(
            'institution', 'stats', 'studentsByProgram', 'enrollmentTrend',
            'revenueTrend', 'studentsByStatus', 'recentStudents', 'recentPayments', 'staffByDept'
        ));
    }
}
