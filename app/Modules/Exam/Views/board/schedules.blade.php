@extends('core::layouts.app')
@section('title', 'Exam Schedules')
@section('page_title', 'Exam Schedules')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-gray-500 text-sm">{{ $schedules->total() }} scheduled exams</p>
    <a href="{{ route('exam.schedules.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i> New Schedule
    </a>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($schedules as $s)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="font-medium text-gray-900">{{ $s->course_name }}</div>
                    <div class="text-xs text-gray-400">{{ $s->course_code }}</div>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $s->exam_type }}</td>
                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($s->exam_date)->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ \Carbon\Carbon::parse($s->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $s->room ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $s->published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $s->published ? 'Published' : 'Draft' }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No exam schedules yet</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $schedules->links() }}</div>
@endsection
