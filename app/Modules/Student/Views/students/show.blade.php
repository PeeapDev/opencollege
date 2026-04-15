@extends('core::layouts.app')
@section('title', 'Student Profile')
@section('page_title', 'Student Profile')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600 text-3xl font-bold">{{ substr($student->user->name ?? 'S', 0, 1) }}</div>
        <h3 class="text-lg font-semibold text-gray-900">{{ $student->user->name ?? 'Unknown' }}</h3>
        <p class="text-sm text-gray-500">{{ $student->student_id }}</p>
        <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs capitalize {{ $student->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $student->status }}</span>
        <div class="mt-4 text-left space-y-2 text-sm">
            <p><span class="text-gray-500">Email:</span> <span class="text-gray-900">{{ $student->user->email ?? '—' }}</span></p>
            <p><span class="text-gray-500">Phone:</span> <span class="text-gray-900">{{ $student->phone ?? '—' }}</span></p>
            <p><span class="text-gray-500">Gender:</span> <span class="text-gray-900 capitalize">{{ $student->gender ?? '—' }}</span></p>
            <p><span class="text-gray-500">DOB:</span> <span class="text-gray-900">{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</span></p>
            <p><span class="text-gray-500">Admitted:</span> <span class="text-gray-900">{{ $student->admission_date?->format('d M Y') ?? '—' }}</span></p>
        </div>
        <div class="mt-4 flex gap-2 justify-center">
            <a href="{{ route('students.edit', $student) }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-edit mr-1"></i>Edit</a>
            <a href="{{ route('students.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg">Back</a>
        </div>
    </div>
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h4 class="font-semibold text-gray-900 mb-3">Academic Info</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <p><span class="text-gray-500">Program:</span> <span class="text-gray-900">{{ $student->program->name ?? '—' }}</span></p>
                <p><span class="text-gray-500">Department:</span> <span class="text-gray-900">{{ $student->program->department->name ?? '—' }}</span></p>
                <p><span class="text-gray-500">Faculty:</span> <span class="text-gray-900">{{ $student->program->department->faculty->name ?? '—' }}</span></p>
                <p><span class="text-gray-500">Year/Semester:</span> <span class="text-gray-900">Year {{ $student->current_year }} / Sem {{ $student->current_semester }}</span></p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h4 class="font-semibold text-gray-900 mb-3">Enrolled Courses</h4>
            @if($student->enrollments->count())
                <div class="space-y-2">
                    @foreach($student->enrollments as $e)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div><span class="font-mono text-blue-600 text-sm">{{ $e->courseSection->course->code ?? '' }}</span> <span class="text-gray-900 text-sm">{{ $e->courseSection->course->name ?? '' }}</span></div>
                            <span class="px-2 py-0.5 rounded-full text-xs capitalize {{ $e->status === 'enrolled' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $e->status }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">No courses enrolled.</p>
            @endif
        </div>
    </div>
</div>
@endsection
