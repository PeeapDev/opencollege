<?php

namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Faculty;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::where('institution_id', auth()->user()->current_institution_id)
            ->with('faculty', 'head')
            ->latest()
            ->paginate(20);

        return view('academic::departments.index', compact('departments'));
    }

    public function create()
    {
        $faculties = Faculty::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->get();
        return view('academic::departments.create', compact('faculties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'faculty_id' => 'required|exists:faculties,id',
            'description' => 'nullable|string',
        ]);

        Department::create(array_merge($request->only('name', 'code', 'faculty_id', 'description'), [
            'institution_id' => auth()->user()->current_institution_id,
        ]));

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        $faculties = Faculty::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->get();
        return view('academic::departments.edit', compact('department', 'faculties'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'faculty_id' => 'required|exists:faculties,id',
            'description' => 'nullable|string',
        ]);

        $department->update($request->only('name', 'code', 'faculty_id', 'description'));
        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
