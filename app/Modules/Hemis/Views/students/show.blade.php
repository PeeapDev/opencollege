@extends('hemis::layout')
@section('title', $student->user->name ?? 'Student')
@section('page_title', $student->user->name ?? 'Student Profile')
@section('subtitle', $student->nsi_number ? 'NSI: ' . $student->nsi_number : 'Matric: ' . $student->student_id)

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Identity --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 lg:col-span-1">
        <div class="text-center mb-4">
            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 mx-auto flex items-center justify-center text-white text-2xl font-bold">
                {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
            </div>
            <h2 class="mt-3 text-lg font-semibold text-slate-900">{{ $student->user->name ?? '—' }}</h2>
            <p class="text-sm text-slate-500">{{ $student->user->email ?? '' }}</p>
        </div>

        <dl class="space-y-2 text-sm pt-4 border-t border-slate-100">
            <div class="flex justify-between"><dt class="text-slate-500">NSI</dt><dd class="font-mono text-xs">{{ $student->nsi_number ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Matric No</dt><dd class="font-mono text-xs">{{ $student->student_id }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd class="capitalize">{{ $student->status }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Gender</dt><dd class="capitalize">{{ $student->gender ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">DOB</dt><dd>{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Phone</dt><dd>{{ $student->phone ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Admission</dt><dd>{{ $student->admission_date?->format('d M Y') ?? '—' }}</dd></div>
        </dl>
    </div>

    {{-- Academic --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 lg:col-span-2">
        <h3 class="text-base font-semibold text-slate-900 mb-4">Academic Affiliation</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6 text-sm">
            <div>
                <dt class="text-xs text-slate-500 uppercase">Institution</dt>
                <dd class="text-slate-900 mt-0.5">
                    @if($student->institution)
                        <a href="{{ route('hemis.institutions.show', $student->institution->id) }}" class="hover:text-blue-600">
                            {{ $student->institution->name }}
                        </a>
                    @else — @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs text-slate-500 uppercase">Program</dt>
                <dd class="text-slate-900 mt-0.5">{{ $student->program->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-500 uppercase">Department</dt>
                <dd class="text-slate-900 mt-0.5">{{ $student->program->department->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-500 uppercase">Faculty</dt>
                <dd class="text-slate-900 mt-0.5">{{ $student->program->department->faculty->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-500 uppercase">Level</dt>
                <dd class="text-slate-900 mt-0.5 capitalize">{{ $student->program->level ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-500 uppercase">Duration</dt>
                <dd class="text-slate-900 mt-0.5">{{ $student->program->duration_years ?? '—' }} years</dd>
            </div>
        </dl>

        <div class="mt-6 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">Academic records</span>
                <span class="text-slate-700">
                    {{ $student->enrollments->count() }} enrolments &middot;
                    {{ $student->grades->count() }} grade records
                </span>
            </div>
        </div>

        @if ($student->institution)
            <div class="mt-4 p-3 bg-slate-50 rounded-lg text-xs text-slate-600">
                Operational records for this student live on the institution's portal:
                <a href="https://{{ $student->institution->domain }}.college.edu.sl" target="_blank" class="text-blue-600 hover:underline ml-1">
                    {{ $student->institution->domain }}.college.edu.sl ↗
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
