@extends('core::layouts.app')
@section('title', 'Payroll')
@section('page_title', 'Payroll Management')

@section('content')
<div class="grid lg:grid-cols-3 gap-6 mb-6">
    {{-- Run Payroll Card --}}
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Run Monthly Payroll</h3>
        <form method="POST" action="{{ route('hr.payroll.run') }}" class="space-y-3">
            @csrf
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Month</label>
                <select name="month" class="w-full px-3 py-2 border rounded-lg text-sm">
                    @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                    <option value="{{ $m }}" {{ now()->format('F') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Year</label>
                <input type="number" name="year" value="{{ now()->year }}" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                <i class="fas fa-play mr-1"></i> Process Payroll
            </button>
        </form>
        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
            <p class="text-xs text-blue-700"><i class="fas fa-info-circle mr-1"></i> Total monthly payroll: <strong>{{ number_format($totalPayroll, 2) }} NLE</strong></p>
        </div>
    </div>

    {{-- Payroll History --}}
    <div class="lg:col-span-2 bg-white rounded-xl border">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-semibold text-gray-900">Payroll History</h3>
        </div>
        @if(isset($payrollHistory) && $payrollHistory->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr><th class="px-5 py-2 text-left">Period</th><th class="px-5 py-2 text-left">Staff</th><th class="px-5 py-2 text-left">Amount</th><th class="px-5 py-2 text-left">Status</th><th class="px-5 py-2 text-left">Date</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($payrollHistory as $run)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-2.5 font-medium text-gray-900">{{ $run->month }} {{ $run->year }}</td>
                        <td class="px-5 py-2.5 text-gray-500">{{ $run->total_staff }}</td>
                        <td class="px-5 py-2.5 text-gray-700 font-mono">{{ number_format($run->total_amount, 2) }}</td>
                        <td class="px-5 py-2.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $run->status === 'paid' ? 'bg-green-100 text-green-700' : ($run->status === 'processed' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">{{ ucfirst($run->status) }}</span>
                        </td>
                        <td class="px-5 py-2.5 text-gray-400 text-xs">{{ \Carbon\Carbon::parse($run->created_at)->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-12 text-center">
            <i class="fas fa-receipt text-gray-200 text-3xl mb-2"></i>
            <p class="text-sm text-gray-400">No payroll runs yet</p>
        </div>
        @endif
    </div>
</div>

{{-- Staff Salary List --}}
<div class="bg-white rounded-xl border">
    <div class="px-5 py-4 border-b">
        <h3 class="text-sm font-semibold text-gray-900">Staff Salary Overview</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr><th class="px-5 py-2 text-left">Name</th><th class="px-5 py-2 text-left">Department</th><th class="px-5 py-2 text-left">Designation</th><th class="px-5 py-2 text-left">Type</th><th class="px-5 py-2 text-right">Basic Salary</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($staff as $s)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-2.5 text-gray-900 font-medium">{{ $s->user->name ?? '—' }}</td>
                    <td class="px-5 py-2.5 text-gray-500 text-xs">{{ $s->department->name ?? '—' }}</td>
                    <td class="px-5 py-2.5 text-gray-500 text-xs">{{ $s->designation->title ?? '—' }}</td>
                    <td class="px-5 py-2.5"><span class="px-2 py-0.5 text-xs rounded-full bg-blue-50 text-blue-700">{{ ucfirst(str_replace('_', ' ', $s->staff_type)) }}</span></td>
                    <td class="px-5 py-2.5 text-right font-mono text-gray-700">{{ number_format($s->basic_salary ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $staff->links() }}</div>
</div>
@endsection
