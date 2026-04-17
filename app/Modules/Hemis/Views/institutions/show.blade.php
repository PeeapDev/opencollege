@extends('hemis::layout')
@section('title', $institution->name)
@section('page_title', $institution->name)
@section('subtitle', $institution->code . ' &middot; ' . ucfirst($institution->type) . ' &middot; ' . ($institution->city ?? 'Sierra Leone'))

@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="text-xs text-slate-500 uppercase">Students</div>
        <div class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($stats['students']) }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="text-xs text-slate-500 uppercase">Programs</div>
        <div class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($stats['programs']) }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="text-xs text-slate-500 uppercase">Staff</div>
        <div class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($stats['staff']) }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Basic info --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-base font-semibold text-slate-900 mb-4">Institution Profile</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Name</dt><dd class="text-slate-900">{{ $institution->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Code</dt><dd class="text-slate-900">{{ $institution->code }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Type</dt><dd class="text-slate-900 capitalize">{{ $institution->type }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Portal</dt>
                <dd><a href="https://{{ $institution->domain }}.college.edu.sl" target="_blank" class="text-blue-600 hover:underline">{{ $institution->domain }}.college.edu.sl ↗</a></dd>
            </div>
            <div class="flex justify-between"><dt class="text-slate-500">Accreditation</dt><dd class="capitalize">{{ str_replace('_',' ', $institution->accreditation_status ?: 'unknown') }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Registration #</dt><dd class="text-slate-900">{{ $institution->registration_number ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd>{{ $institution->email ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Phone</dt><dd>{{ $institution->phone ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Address</dt><dd class="text-right">{{ $institution->address ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">City / Country</dt><dd>{{ trim(($institution->city ?? '') . ', ' . ($institution->country ?? ''), ', ') ?: '—' }}</dd></div>
        </dl>
    </div>

    {{-- Programs list --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-base font-semibold text-slate-900 mb-4">Programs ({{ $programs->count() }})</h3>
        @if ($programs->isEmpty())
            <p class="text-sm text-slate-500">No programs registered yet.</p>
        @else
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @foreach ($programs as $p)
                    <div class="flex justify-between text-sm py-2 border-b border-slate-100 last:border-0">
                        <div>
                            <div class="font-medium text-slate-900">{{ $p->name }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $p->department->faculty->name ?? '' }}
                                @if ($p->department) &middot; {{ $p->department->name }} @endif
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 whitespace-nowrap">
                            <span class="capitalize">{{ $p->level }}</span> &middot; {{ $p->duration_years }}y
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
