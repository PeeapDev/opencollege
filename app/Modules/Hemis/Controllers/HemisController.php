<?php

namespace App\Modules\Hemis\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Institution;
use App\Modules\Student\Models\Student;
use App\Modules\Academic\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * HEMIS — Higher Education MIS (government portal) controller.
 *
 * Only active on the root domain college.edu.sl (institution id=1).
 * Presents cross-institution data: dashboard, institution directory,
 * student search, national reports.
 */
class HemisController extends Controller
{
    public function dashboard()
    {
        // Exclude the platform institution (id=1) from counts
        $institutionsQ = Institution::where('id', '!=', 1);

        $stats = [
            'institutions'       => (clone $institutionsQ)->count(),
            'institutions_active' => (clone $institutionsQ)->where('active', true)->count(),
            'students'           => Student::count(),
            'programs'           => Program::count(),
            'users'              => User::count(),
        ];

        // Institution type breakdown
        $byType = (clone $institutionsQ)
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // Accreditation status breakdown
        $byAccreditation = (clone $institutionsQ)
            ->select('accreditation_status', DB::raw('COUNT(*) as total'))
            ->groupBy('accreditation_status')
            ->pluck('total', 'accreditation_status')
            ->toArray();

        // Recent institutions (last 10)
        $recentInstitutions = (clone $institutionsQ)
            ->latest()->limit(10)
            ->get(['id', 'name', 'code', 'domain', 'type', 'accreditation_status', 'active', 'created_at']);

        // Student gender split (across all institutions)
        $byGender = Student::select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')->pluck('total', 'gender')->toArray();

        return view('hemis::dashboard', compact('stats', 'byType', 'byAccreditation', 'recentInstitutions', 'byGender'));
    }

    public function institutions(Request $request)
    {
        $q = Institution::where('id', '!=', 1);

        if ($search = $request->query('search')) {
            $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('domain', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%");
            });
        }

        if ($type = $request->query('type')) {
            $q->where('type', $type);
        }

        if ($accreditation = $request->query('accreditation')) {
            $q->where('accreditation_status', $accreditation);
        }

        $institutions = $q->latest()->paginate(20)->withQueryString();

        return view('hemis::institutions.index', compact('institutions', 'search', 'type', 'accreditation'));
    }

    public function institutionShow(Institution $institution)
    {
        abort_if($institution->id == 1, 404);

        $stats = [
            'students'  => Student::where('institution_id', $institution->id)->count(),
            'programs'  => Program::where('institution_id', $institution->id)->count(),
            'staff'     => DB::table('user_roles')
                ->where('institution_id', $institution->id)
                ->whereNotIn('role_id', function ($sub) {
                    $sub->select('id')->from('roles')->whereIn('name', ['student']);
                })
                ->distinct('user_id')->count('user_id'),
        ];

        $programs = Program::where('institution_id', $institution->id)
            ->with('department.faculty')
            ->get();

        return view('hemis::institutions.show', compact('institution', 'stats', 'programs'));
    }

    public function students(Request $request)
    {
        $q = Student::with(['user', 'program.department.faculty', 'institution']);

        if ($search = $request->query('search')) {
            $q->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%$search%")
                  ->orWhere('nsi_number', 'like', "%$search%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                  });
            });
        }

        if ($institutionId = $request->query('institution')) {
            $q->where('institution_id', $institutionId);
        }

        $students = $q->latest()->paginate(25)->withQueryString();
        $institutions = Institution::where('id', '!=', 1)->orderBy('name')->get(['id', 'name', 'code']);

        return view('hemis::students.index', compact('students', 'institutions', 'search', 'institutionId'));
    }

    public function studentShow($identifier)
    {
        $student = Student::with(['user', 'program.department.faculty', 'institution', 'enrollments', 'grades'])
            ->where('nsi_number', $identifier)
            ->orWhere('student_id', $identifier)
            ->first();

        abort_unless($student, 404, 'Student not found in any HEMIS-registered institution.');

        return view('hemis::students.show', compact('student'));
    }

    public function reports()
    {
        return view('hemis::reports.index');
    }

    public function reportEnrollment()
    {
        $byInstitution = Student::select('institution_id', DB::raw('COUNT(*) as total'))
            ->groupBy('institution_id')
            ->with('institution:id,name,code')
            ->get()
            ->map(fn ($row) => [
                'institution' => $row->institution->name ?? 'Unknown',
                'code'        => $row->institution->code ?? '',
                'total'       => $row->total,
            ]);

        $byLevel = Student::join('programs', 'programs.id', '=', 'students.program_id')
            ->select('programs.level', DB::raw('COUNT(*) as total'))
            ->groupBy('programs.level')
            ->pluck('total', 'level')->toArray();

        $byGender = Student::select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')->pluck('total', 'gender')->toArray();

        return view('hemis::reports.enrollment', compact('byInstitution', 'byLevel', 'byGender'));
    }
}
