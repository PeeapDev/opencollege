<?php

namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('institution_id', auth()->user()->current_institution_id)
            ->with('department')
            ->latest()
            ->paginate(20);
        return view('academic::courses.index', compact('courses'));
    }

    public function create()
    {
        $departments = Department::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->get();
        return view('academic::courses.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses,code',
            'department_id' => 'required|exists:departments,id',
            'credit_hours' => 'required|integer|min:1|max:12',
            'type' => 'required|in:core,elective,general',
        ]);

        Course::create(array_merge($request->only('name', 'code', 'department_id', 'credit_hours', 'type', 'year_level', 'semester_number', 'description'), [
            'institution_id' => auth()->user()->current_institution_id,
        ]));

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $departments = Department::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->get();
        return view('academic::courses.edit', compact('course', 'departments'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses,code,' . $course->id,
            'department_id' => 'required|exists:departments,id',
            'credit_hours' => 'required|integer|min:1|max:12',
            'type' => 'required|in:core,elective,general',
        ]);
        $course->update($request->only('name', 'code', 'department_id', 'credit_hours', 'type', 'year_level', 'semester_number', 'description'));
        return redirect()->route('courses.index')->with('success', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted.');
    }
}
