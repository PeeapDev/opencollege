<?php

namespace App\Modules\Staff\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Staff\Models\Staff;
use App\Modules\Staff\Models\Designation;
use App\Modules\Academic\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::where('institution_id', auth()->user()->current_institution_id)
            ->with('user', 'department', 'designation')
            ->latest()
            ->paginate(20);
        return view('staff::staff.index', compact('staff'));
    }

    public function create()
    {
        $departments = Department::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->get();
        $designations = Designation::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->get();
        return view('staff::staff.create', compact('departments', 'designations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'staff_id_number' => 'required|string|max:30|unique:staff,staff_id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'staff_type' => 'required|in:academic,non_academic,admin',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('staff123'),
                'current_institution_id' => auth()->user()->current_institution_id,
            ]);

            Staff::create([
                'user_id' => $user->id,
                'institution_id' => auth()->user()->current_institution_id,
                'staff_id' => $request->staff_id_number,
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'staff_type' => $request->staff_type,
                'joining_date' => $request->joining_date ?? now(),
                'gender' => $request->gender,
                'phone' => $request->phone,
                'qualification' => $request->qualification,
                'basic_salary' => $request->basic_salary,
                'status' => 'active',
            ]);
        });

        return redirect()->route('staff.index')->with('success', 'Staff member added. Default password: staff123');
    }

    public function show(Staff $staff)
    {
        $staff->load('user', 'department.faculty', 'designation', 'teachingAssignments.courseSection.course');
        return view('staff::staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        $staff->load('user');
        $departments = Department::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->get();
        $designations = Designation::where('institution_id', auth()->user()->current_institution_id)->where('active', true)->get();
        return view('staff::staff.edit', compact('staff', 'departments', 'designations'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->user_id,
            'status' => 'required|in:active,on_leave,resigned,terminated,retired',
        ]);

        DB::transaction(function () use ($request, $staff) {
            $staff->user->update($request->only('name', 'email'));
            $staff->update($request->only('department_id', 'designation_id', 'staff_type', 'status', 'gender', 'phone', 'qualification', 'basic_salary'));
        });

        return redirect()->route('staff.index')->with('success', 'Staff updated.');
    }

    public function destroy(Staff $staff)
    {
        $staff->user->delete();
        return redirect()->route('staff.index')->with('success', 'Staff removed.');
    }
}
