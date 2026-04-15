<?php

namespace App\Modules\Exam\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Exam\Models\Exam;
use App\Modules\Exam\Models\Grade;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::where('institution_id', auth()->user()->current_institution_id)
            ->with('courseSection.course', 'examType', 'semester')
            ->latest()
            ->paginate(20);
        return view('exam::exams.index', compact('exams'));
    }

    public function create()
    {
        return view('exam::exams.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('exams.index')->with('success', 'Exam created.');
    }

    public function show(Exam $exam)
    {
        $exam->load('grades.student.user', 'courseSection.course');
        return view('exam::exams.show', compact('exam'));
    }
}
