<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Admission;
use App\Modules\Student\Models\Student;
use App\Modules\Academic\Models\Program;
use App\Modules\Settings\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdmissionController extends Controller
{
    public function index()
    {
        $institutionId = auth()->user()->current_institution_id;
        $admissions = Admission::where('institution_id', $institutionId)
            ->with('program')
            ->latest()
            ->paginate(20);
        $counts = [
            'pending' => Admission::where('institution_id', $institutionId)->where('status', 'pending')->count(),
            'accepted' => Admission::where('institution_id', $institutionId)->where('status', 'accepted')->count(),
            'rejected' => Admission::where('institution_id', $institutionId)->where('status', 'rejected')->count(),
            'enrolled' => Admission::where('institution_id', $institutionId)->where('status', 'enrolled')->count(),
        ];
        return view('student::admissions.index', compact('admissions', 'counts'));
    }

    public function show(Admission $admission)
    {
        $admission->load('program', 'reviewer');
        return view('student::admissions.show', compact('admission'));
    }

    public function accept(Admission $admission)
    {
        $admission->update([
            'status' => 'accepted',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        return back()->with('success', "Application {$admission->application_number} accepted.");
    }

    public function reject(Request $request, Admission $admission)
    {
        $admission->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        return back()->with('success', "Application {$admission->application_number} rejected.");
    }

    public function enroll(Admission $admission)
    {
        if ($admission->status !== 'accepted') {
            return back()->with('error', 'Only accepted applications can be enrolled.');
        }

        DB::transaction(function () use ($admission) {
            $user = User::create([
                'name' => trim("{$admission->first_name} {$admission->middle_name} {$admission->last_name}"),
                'email' => $admission->email,
                'phone' => $admission->phone,
                'password' => Hash::make('student123'),
                'current_institution_id' => $admission->institution_id,
            ]);

            $studentRole = \App\Modules\Settings\Models\Role::where('institution_id', $admission->institution_id)
                ->where('name', 'student')->first();
            if ($studentRole) {
                $user->roles()->attach($studentRole->id, ['institution_id' => $admission->institution_id]);
            }

            $matric = 'OC-' . date('Y') . '-' . str_pad(Student::where('institution_id', $admission->institution_id)->count() + 1, 4, '0', STR_PAD_LEFT);

            Student::create([
                'user_id' => $user->id,
                'institution_id' => $admission->institution_id,
                'student_id' => $matric,
                'nsi_number' => $admission->nsi_number,
                'program_id' => $admission->program_id,
                'status' => 'active',
                'admission_date' => now(),
                'current_year' => 1,
                'current_semester' => 1,
                'gender' => $admission->gender,
                'date_of_birth' => $admission->date_of_birth,
                'nationality' => $admission->nationality,
                'national_id' => $admission->national_id,
                'address' => $admission->address,
                'city' => $admission->city,
                'phone' => $admission->phone,
                'guardian_name' => $admission->guardian_name,
                'guardian_phone' => $admission->guardian_phone,
                'guardian_email' => $admission->guardian_email,
                'guardian_relation' => $admission->guardian_relation,
            ]);

            $admission->update(['status' => 'enrolled']);
        });

        return back()->with('success', "Student enrolled successfully. Default password: student123");
    }

    // Public admission form
    public function publicForm($slug = null)
    {
        $institution = null;
        if ($slug) {
            $institution = Institution::where('domain', $slug)->where('active', true)->first();
        } else {
            $institution = app()->bound('institution') ? app('institution') : null;
        }

        if (!$institution || $institution->id == 1) {
            abort(404, 'College not found.');
        }

        $settings = DB::table('admission_settings')->where('institution_id', $institution->id)->first();
        if (!$settings || !$settings->is_open) {
            return view('student::admissions.closed', compact('institution'));
        }

        $programs = Program::where('institution_id', $institution->id)->where('active', true)->orderBy('name')->get();
        return view('student::admissions.apply', compact('institution', 'programs', 'settings'));
    }

    public function publicSubmit(Request $request)
    {
        $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:30',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'program_id' => 'required|exists:programs,id',
        ]);

        $appNo = 'APP-' . date('Y') . '-' . strtoupper(Str::random(6));

        Admission::create(array_merge($request->only([
            'institution_id', 'first_name', 'last_name', 'middle_name', 'email', 'phone',
            'date_of_birth', 'gender', 'nationality', 'national_id', 'nsi_number',
            'address', 'city', 'program_id', 'guardian_name', 'guardian_phone',
            'guardian_email', 'guardian_relation',
        ]), [
            'application_number' => $appNo,
            'status' => 'pending',
            'academic_year' => date('Y') . '/' . (date('Y') + 1),
        ]));

        return back()->with('success', "Application submitted! Your application number is: {$appNo}. Keep this for tracking.");
    }
}
