@extends('core::layouts.app')
@section('title', 'HR Dashboard')
@section('page_title', 'Human Resource Dashboard')

@section('content')
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $totalStaff }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Staff</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-green-600">{{ $activeStaff }}</p>
        <p class="text-xs text-gray-500 mt-1">Active</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-amber-600">{{ $onLeave }}</p>
        <p class="text-xs text-gray-500 mt-1">On Leave</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-red-600">{{ $pendingLeaves }}</p>
        <p class="text-xs text-gray-500 mt-1">Pending Leaves</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-purple-600">{{ $departments }}</p>
        <p class="text-xs text-gray-500 mt-1">Departments</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-lg font-bold text-indigo-600">{{ number_format($totalPayroll, 0) }}</p>
        <p class="text-xs text-gray-500 mt-1">Monthly Payroll (NLE)</p>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('hr.leaves') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 transition">
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-calendar-check text-amber-600 text-sm"></i></div>
                <div><p class="text-sm font-medium text-gray-900">Leave Management</p><p class="text-xs text-gray-400">{{ $pendingLeaves }} pending</p></div>
            </a>
            <a href="{{ route('hr.payroll') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 transition">
                <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-money-check-alt text-green-600 text-sm"></i></div>
                <div><p class="text-sm font-medium text-gray-900">Payroll</p><p class="text-xs text-gray-400">{{ number_format($totalPayroll, 0) }} NLE/month</p></div>
            </a>
            <a href="{{ route('hr.directory') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 transition">
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-users text-blue-600 text-sm"></i></div>
                <div><p class="text-sm font-medium text-gray-900">Staff Directory</p><p class="text-xs text-gray-400">{{ $totalStaff }} staff members</p></div>
            </a>
            <a href="{{ route('staff.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 transition">
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-user-plus text-purple-600 text-sm"></i></div>
                <div><p class="text-sm font-medium text-gray-900">Add New Staff</p><p class="text-xs text-gray-400">Manage staff records</p></div>
            </a>
        </div>
    </div>

    {{-- Recent Leave Requests --}}
    <div class="lg:col-span-2 bg-white rounded-xl border">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Leave Requests</h3>
            <a href="{{ route('hr.leaves') }}" class="text-xs text-blue-600 hover:underline">View All</a>
        </div>
        @if($recentLeaves->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr><th class="px-5 py-2 text-left">Staff</th><th class="px-5 py-2 text-left">Type</th><th class="px-5 py-2 text-left">Dates</th><th class="px-5 py-2 text-left">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentLeaves as $leave)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-2.5 text-gray-900">{{ $leave->staff_name }}</td>
                        <td class="px-5 py-2.5 text-gray-500 text-xs">{{ ucfirst($leave->leave_type ?? 'General') }}</td>
                        <td class="px-5 py-2.5 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                        <td class="px-5 py-2.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $leave->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $leave->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $leave->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                            ">{{ ucfirst($leave->status) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-12 text-center">
            <i class="fas fa-calendar-alt text-gray-200 text-3xl mb-2"></i>
            <p class="text-sm text-gray-400">No leave requests yet</p>
        </div>
        @endif
    </div>
</div>
@endsection
