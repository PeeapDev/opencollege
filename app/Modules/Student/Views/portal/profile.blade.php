@extends('core::layouts.app')
@section('title', 'My Profile')
@section('page_title', 'Student Profile')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-center gap-5 mb-6 pb-6 border-b">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-3xl font-bold text-blue-600">
                {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $student->user->name ?? '' }}</h2>
                <p class="text-sm text-gray-500">{{ $student->user->email ?? '' }}</p>
                <p class="text-xs text-gray-400 mt-1">Matric: {{ $student->student_id }} @if($student->nsi_number)• NSI: {{ $student->nsi_number }}@endif</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-700 border-b pb-1">Academic</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-400">Program</span><p class="font-medium text-gray-900">{{ $student->program->name ?? '—' }}</p></div>
                    <div><span class="text-gray-400">Year / Semester</span><p class="font-medium text-gray-900">Year {{ $student->current_year }}, Sem {{ $student->current_semester }}</p></div>
                    <div><span class="text-gray-400">Status</span><p class="font-medium text-gray-900">{{ ucfirst($student->status) }}</p></div>
                    <div><span class="text-gray-400">Admitted</span><p class="font-medium text-gray-900">{{ $student->admission_date?->format('M d, Y') ?? '—' }}</p></div>
                </div>
            </div>
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-700 border-b pb-1">Personal</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-400">Date of Birth</span><p class="font-medium text-gray-900">{{ $student->date_of_birth?->format('M d, Y') ?? '—' }}</p></div>
                    <div><span class="text-gray-400">Gender</span><p class="font-medium text-gray-900">{{ ucfirst($student->gender ?? '—') }}</p></div>
                    <div><span class="text-gray-400">Phone</span><p class="font-medium text-gray-900">{{ $student->phone ?? '—' }}</p></div>
                    <div><span class="text-gray-400">Nationality</span><p class="font-medium text-gray-900">{{ $student->nationality }}</p></div>
                </div>
                <div class="text-sm"><span class="text-gray-400">Address</span><p class="font-medium text-gray-900">{{ $student->address ?? '—' }}, {{ $student->city ?? '' }}</p></div>
            </div>
        </div>
        @if($student->guardian_name)
        <div class="mt-6 pt-4 border-t">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Guardian</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <div><span class="text-gray-400">Name</span><p class="font-medium text-gray-900">{{ $student->guardian_name }}</p></div>
                <div><span class="text-gray-400">Phone</span><p class="font-medium text-gray-900">{{ $student->guardian_phone ?? '—' }}</p></div>
                <div><span class="text-gray-400">Email</span><p class="font-medium text-gray-900">{{ $student->guardian_email ?? '—' }}</p></div>
                <div><span class="text-gray-400">Relation</span><p class="font-medium text-gray-900">{{ ucfirst($student->guardian_relation ?? '—') }}</p></div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
