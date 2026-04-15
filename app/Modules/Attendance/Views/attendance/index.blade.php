@extends('core::layouts.app')
@section('title', 'Attendance')
@section('page_title', 'Attendance')
@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-gray-900">Attendance</h3><p class="text-sm text-gray-500">Track student and staff attendance</p></div>
        <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-plus"></i> Take Attendance</a>
    </div>
    @if($records->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr><th class="px-5 py-3 text-left">Student</th><th class="px-5 py-3 text-left">Course</th><th class="px-5 py-3 text-left">Date</th><th class="px-5 py-3 text-left">Status</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($records as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-900">{{ $r->student->user->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $r->courseSection->course->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $r->date->format('d M Y') }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs capitalize {{ $r->status === 'present' ? 'bg-green-100 text-green-700' : ($r->status === 'absent' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ $r->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $records->links() }}</div>
    @else
    <div class="py-16 text-center"><i class="fas fa-calendar-check text-gray-300 text-4xl mb-4"></i><p class="text-gray-500">No attendance records yet.</p></div>
    @endif
</div>
@endsection
