<?php

namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Models\Department;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::where('institution_id', auth()->user()->current_institution_id)
            ->with('department.faculty')
            ->latest()
            ->paginate(20);
        return view('academic::programs.index', compact('programs'));
    }

    public function create()
    {
        $departments = Department::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->with('faculty')->get();
        return view('academic::programs.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|in:certificate,diploma,higher_diploma,bachelors,masters,doctorate',
            'duration_years' => 'required|integer|min:1|max:10',
            'total_credits' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        Program::create(array_merge($request->only('name', 'code', 'department_id', 'level', 'duration_years', 'total_credits', 'description'), [
            'institution_id' => auth()->user()->current_institution_id,
        ]));

        return redirect()->route('programs.index')->with('success', 'Program created successfully.');
    }

    public function edit(Program $program)
    {
        $departments = Department::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->with('faculty')->get();
        return view('academic::programs.edit', compact('program', 'departments'));
    }

    public function update(Request $request, Program $program)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|in:certificate,diploma,higher_diploma,bachelors,masters,doctorate',
            'duration_years' => 'required|integer|min:1|max:10',
        ]);
        $program->update($request->only('name', 'code', 'department_id', 'level', 'duration_years', 'total_credits', 'description'));
        return redirect()->route('programs.index')->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('programs.index')->with('success', 'Program deleted successfully.');
    }
}
