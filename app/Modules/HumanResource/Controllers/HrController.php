<?php

namespace App\Modules\HumanResource\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Staff\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HrController extends Controller
{
    public function dashboard()
    {
        $instId = auth()->user()->current_institution_id;

        $totalStaff = Staff::where('institution_id', $instId)->count();
        $activeStaff = Staff::where('institution_id', $instId)->where('status', 'active')->count();
        $onLeave = DB::table('leave_requests')
            ->where('institution_id', $instId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();
        $pendingLeaves = DB::table('leave_requests')
            ->where('institution_id', $instId)
            ->where('status', 'pending')
            ->count();
        $totalPayroll = Staff::where('institution_id', $instId)->where('status', 'active')->sum('basic_salary');
        $departments = DB::table('departments')->where('institution_id', $instId)->where('active', true)->count();

        $recentLeaves = DB::table('leave_requests')
            ->join('staff', 'leave_requests.staff_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('leave_requests.institution_id', $instId)
            ->select('leave_requests.*', 'users.name as staff_name')
            ->latest('leave_requests.created_at')
            ->take(10)
            ->get();

        return view('hr::dashboard', compact(
            'totalStaff', 'activeStaff', 'onLeave', 'pendingLeaves',
            'totalPayroll', 'departments', 'recentLeaves'
        ));
    }

    // Leave Management
    public function leaveIndex()
    {
        $instId = auth()->user()->current_institution_id;
        $leaves = DB::table('leave_requests')
            ->join('staff', 'leave_requests.staff_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('leave_requests.institution_id', $instId)
            ->select('leave_requests.*', 'users.name as staff_name')
            ->latest('leave_requests.created_at')
            ->paginate(20);

        return view('hr::leaves.index', compact('leaves'));
    }

    public function leaveApprove($id)
    {
        DB::table('leave_requests')->where('id', $id)->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Leave request approved.');
    }

    public function leaveReject($id)
    {
        DB::table('leave_requests')->where('id', $id)->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Leave request rejected.');
    }

    // Payroll
    public function payroll()
    {
        $instId = auth()->user()->current_institution_id;
        $staff = Staff::where('institution_id', $instId)
            ->where('status', 'active')
            ->with('user', 'department', 'designation')
            ->orderBy('basic_salary', 'desc')
            ->paginate(20);

        $totalPayroll = Staff::where('institution_id', $instId)->where('status', 'active')->sum('basic_salary');

        $payrollHistory = DB::table('payroll_runs')
            ->where('institution_id', $instId)
            ->latest()
            ->take(12)
            ->get();

        return view('hr::payroll.index', compact('staff', 'totalPayroll', 'payrollHistory'));
    }

    public function runPayroll(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|integer',
        ]);

        $instId = auth()->user()->current_institution_id;

        $existing = DB::table('payroll_runs')
            ->where('institution_id', $instId)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->first();

        if ($existing) {
            return back()->with('error', "Payroll for {$request->month} {$request->year} already exists.");
        }

        $activeStaff = Staff::where('institution_id', $instId)->where('status', 'active')->get();
        $totalAmount = $activeStaff->sum('basic_salary');

        $runId = DB::table('payroll_runs')->insertGetId([
            'institution_id' => $instId,
            'month' => $request->month,
            'year' => $request->year,
            'total_staff' => $activeStaff->count(),
            'total_amount' => $totalAmount,
            'status' => 'processed',
            'processed_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($activeStaff as $s) {
            DB::table('payroll_items')->insert([
                'payroll_run_id' => $runId,
                'staff_id' => $s->id,
                'basic_salary' => $s->basic_salary,
                'deductions' => 0,
                'net_salary' => $s->basic_salary,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', "Payroll processed for {$request->month} {$request->year}. {$activeStaff->count()} staff, total: " . number_format($totalAmount, 2) . " NLE");
    }

    // Staff Directory (HR view)
    public function directory()
    {
        $instId = auth()->user()->current_institution_id;
        $staff = Staff::where('institution_id', $instId)
            ->with('user', 'department', 'designation')
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Staff::where('institution_id', $instId)->count(),
            'academic' => Staff::where('institution_id', $instId)->where('staff_type', 'academic')->count(),
            'non_academic' => Staff::where('institution_id', $instId)->where('staff_type', 'non_academic')->count(),
            'admin' => Staff::where('institution_id', $instId)->where('staff_type', 'admin')->count(),
        ];

        return view('hr::directory', compact('staff', 'stats'));
    }
}
