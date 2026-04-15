@extends('core::layouts.app')
@section('title', 'Leave Management')
@section('page_title', 'Leave Management')

@section('content')
<div class="bg-white rounded-xl border">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">All Leave Requests</h3>
        <a href="{{ route('hr.dashboard') }}" class="text-xs text-blue-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i>HR Dashboard</a>
    </div>
    @if($leaves->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-5 py-2.5 text-left">Staff</th>
                    <th class="px-5 py-2.5 text-left">Type</th>
                    <th class="px-5 py-2.5 text-left">Start</th>
                    <th class="px-5 py-2.5 text-left">End</th>
                    <th class="px-5 py-2.5 text-left">Reason</th>
                    <th class="px-5 py-2.5 text-left">Status</th>
                    <th class="px-5 py-2.5 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($leaves as $leave)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-900 font-medium">{{ $leave->staff_name }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ ucfirst($leave->leave_type ?? 'General') }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs max-w-[200px] truncate">{{ $leave->reason ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $leave->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $leave->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $leave->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                        ">{{ ucfirst($leave->status) }}</span>
                    </td>
                    <td class="px-5 py-3">
                        @if($leave->status === 'pending')
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('hr.leaves.approve', $leave->id) }}">@csrf
                                <button class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="POST" action="{{ route('hr.leaves.reject', $leave->id) }}">@csrf
                                <button class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $leaves->links() }}</div>
    @else
    <div class="py-16 text-center">
        <i class="fas fa-calendar-check text-gray-200 text-4xl mb-3"></i>
        <p class="text-gray-400 text-sm">No leave requests found</p>
    </div>
    @endif
</div>
@endsection
