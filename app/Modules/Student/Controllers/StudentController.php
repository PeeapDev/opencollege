<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use App\Modules\Academic\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::where('institution_id', auth()->user()->current_institution_id)
            ->with('user', 'program.department')
            ->latest()
            ->paginate(20);
        return view('student::students.index', compact('students'));
    }

    public function create()
    {
        $programs = Program::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->with('department')->get();
        return view('student::students.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'student_id_number' => 'required|string|max:30|unique:students,student_id',
            'program_id' => 'required|exists:programs,id',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        $tempPassword = Str::random(12);
        DB::transaction(function () use ($request, $tempPassword) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($tempPassword),
                'current_institution_id' => auth()->user()->current_institution_id,
            ]);

            Student::create([
                'user_id' => $user->id,
                'institution_id' => auth()->user()->current_institution_id,
                'student_id' => $request->student_id_number,
                'program_id' => $request->program_id,
                'admission_date' => now(),
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Student admitted. Temporary password sent to student (shown once): '.$tempPassword);
    }

    public function show(Student $student)
    {
        $student->load('user', 'program.department.faculty', 'enrollments.courseSection.course', 'grades', 'invoices', 'cgpaRecords');
        return view('student::students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $student->load('user');
        $programs = Program::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->with('department')->get();
        return view('student::students.edit', compact('student', 'programs'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'program_id' => 'required|exists:programs,id',
            'status' => 'required|in:active,suspended,withdrawn,graduated,deferred',
        ]);

        DB::transaction(function () use ($request, $student) {
            $student->user->update($request->only('name', 'email'));
            $student->update($request->only('program_id', 'status', 'gender', 'date_of_birth', 'phone', 'address', 'current_year', 'current_semester'));
        });

        return redirect()->route('students.index')->with('success', 'Student admitted. Temporary password sent to student (shown once): '.$tempPassword);
    }

    public function destroy(Student $student)
    {
        $student->user->delete();
        return redirect()->route('students.index')->with('success', 'Student admitted. Temporary password sent to student (shown once): '.$tempPassword);
    }
}
