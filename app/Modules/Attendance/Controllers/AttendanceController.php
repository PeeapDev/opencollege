<?php

namespace App\Modules\Attendance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $records = Attendance::where('institution_id', auth()->user()->current_institution_id)
            ->with('courseSection.course', 'student.user')
            ->latest()
            ->paginate(20);
        return view('attendance::attendance.index', compact('records'));
    }

    public function create()
    {
        return view('attendance::attendance.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('attendance.index')->with('success', 'Attendance recorded.');
    }
}
