<?php

namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index()
    {
        $faculties = Faculty::where('institution_id', auth()->user()->current_institution_id)
            ->with('dean', 'departments')
            ->latest()
            ->paginate(20);

        return view('academic::faculties.index', compact('faculties'));
    }

    public function create()
    {
        return view('academic::faculties.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        Faculty::create([
            'institution_id' => auth()->user()->current_institution_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return redirect()->route('faculties.index')->with('success', 'Faculty created successfully.');
    }

    public function edit(Faculty $faculty)
    {
        return view('academic::faculties.edit', compact('faculty'));
    }

    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $faculty->update($request->only('name', 'code', 'description'));

        return redirect()->route('faculties.index')->with('success', 'Faculty updated successfully.');
    }

    public function destroy(Faculty $faculty)
    {
        $faculty->delete();
        return redirect()->route('faculties.index')->with('success', 'Faculty deleted successfully.');
    }
}
