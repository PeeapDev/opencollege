<?php

namespace App\Modules\Exam\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Exam\Models\Exam;
use App\Modules\Exam\Models\ExamType;
use App\Modules\Exam\Models\Grade;
use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Semester;
use App\Modules\Student\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamBoardController extends Controller
{
    public function schedules()
    {
        $institutionId = auth()->user()->current_institution_id;
        $schedules = DB::table('exam_schedules')
            ->where('exam_schedules.institution_id', $institutionId)
            ->join('exam_types', 'exam_schedules.exam_type_id', '=', 'exam_types.id')
            ->join('courses', 'exam_schedules.course_id', '=', 'courses.id')
            ->select('exam_schedules.*', 'exam_types.name as exam_type', 'courses.name as course_name', 'courses.code as course_code')
            ->orderBy('exam_date')
            ->paginate(20);
        return view('exam::board.schedules', compact('schedules'));
    }

    public function createSchedule()
    {
        $institutionId = auth()->user()->current_institution_id;
        $examTypes = ExamType::where('institution_id', $institutionId)->get();
        $courses = Course::where('institution_id', $institutionId)->where('active', true)->get();
        $semesters = Semester::where('institution_id', $institutionId)->get();
        return view('exam::board.create_schedule', compact('examTypes', 'courses', 'semesters'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        DB::table('exam_schedules')->insert([
            'institution_id' => auth()->user()->current_institution_id,
            'exam_type_id' => $request->exam_type_id,
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
            'invigilator' => $request->invigilator,
            'published' => $request->boolean('published'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('exam.schedules')->with('success', 'Exam schedule created.');
    }

    public function grading()
    {
        $institutionId = auth()->user()->current_institution_id;
        $courses = Course::where('institution_id', $institutionId)->where('active', true)->orderBy('name')->get();
        $examTypes = ExamType::where('institution_id', $institutionId)->get();
        $semesters = Semester::where('institution_id', $institutionId)->get();
        return view('exam::board.grading', compact('courses', 'examTypes', 'semesters'));
    }

    public function loadStudentsForGrading(Request $request)
    {
        $students = Student::where('students.institution_id', auth()->user()->current_institution_id)
            ->where('students.status', 'active')
            ->join('enrollments', 'students.id', '=', 'enrollments.student_id')
            ->join('course_sections', 'enrollments.course_section_id', '=', 'course_sections.id')
            ->where('course_sections.course_id', $request->course_id)
            ->join('users', 'students.user_id', '=', 'users.id')
            ->select('students.id', 'students.student_id as matric', 'users.name')
            ->get();

        // If no enrollments, get all active students
        if ($students->isEmpty()) {
            $students = Student::where('students.institution_id', auth()->user()->current_institution_id)
                ->where('status', 'active')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->select('students.id', 'students.student_id as matric', 'users.name')
                ->orderBy('users.name')
                ->get();
        }

        $existingGrades = Grade::where('course_id', $request->course_id)
            ->where('exam_type_id', $request->exam_type_id)
            ->pluck('score', 'student_id')
            ->toArray();

        return response()->json(['students' => $students, 'existing_grades' => $existingGrades]);
    }

    public function saveGrades(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'exam_type_id' => 'required|exists:exam_types,id',
            'semester_id' => 'required|exists:semesters,id',
            'grades' => 'required|array',
        ]);

        $institutionId = auth()->user()->current_institution_id;

        foreach ($request->grades as $studentId => $score) {
            if ($score === null || $score === '') continue;

            $letterGrade = $this->scoreToGrade((float)$score);

            Grade::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'course_id' => $request->course_id,
                    'exam_type_id' => $request->exam_type_id,
                ],
                [
                    'institution_id' => $institutionId,
                    'semester_id' => $request->semester_id,
                    'score' => $score,
                    'letter_grade' => $letterGrade,
                    'credit_hours' => 3,
                    'graded_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Grades saved successfully.');
    }

    public function results()
    {
        $institutionId = auth()->user()->current_institution_id;
        $publications = DB::table('result_publications')
            ->where('result_publications.institution_id', $institutionId)
            ->join('exam_types', 'result_publications.exam_type_id', '=', 'exam_types.id')
            ->join('semesters', 'result_publications.semester_id', '=', 'semesters.id')
            ->select('result_publications.*', 'exam_types.name as exam_type', 'semesters.name as semester_name')
            ->latest('result_publications.created_at')
            ->paginate(20);
        return view('exam::board.results', compact('publications'));
    }

    public function publishResults(Request $request)
    {
        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        DB::table('result_publications')->updateOrInsert(
            [
                'institution_id' => auth()->user()->current_institution_id,
                'exam_type_id' => $request->exam_type_id,
                'semester_id' => $request->semester_id,
            ],
            [
                'is_published' => true,
                'published_at' => now(),
                'published_by' => auth()->id(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Results published.');
    }

    protected function scoreToGrade(float $score): string
    {
        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'A-';
        if ($score >= 75) return 'B+';
        if ($score >= 70) return 'B';
        if ($score >= 65) return 'B-';
        if ($score >= 60) return 'C+';
        if ($score >= 55) return 'C';
        if ($score >= 50) return 'C-';
        if ($score >= 45) return 'D+';
        if ($score >= 40) return 'D';
        return 'F';
    }
}
