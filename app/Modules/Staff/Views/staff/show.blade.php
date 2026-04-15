@extends('core::layouts.app')
@section('title', 'Staff Profile')
@section('page_title', 'Staff Profile')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
        <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-600 text-3xl font-bold">{{ substr($staff->user->name ?? 'S', 0, 1) }}</div>
        <h3 class="text-lg font-semibold text-gray-900">{{ $staff->user->name ?? 'Unknown' }}</h3>
        <p class="text-sm text-gray-500">{{ $staff->staff_id }}</p>
        <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs capitalize {{ $staff->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $staff->status }}</span>
        <div class="mt-4 text-left space-y-2 text-sm">
            <p><span class="text-gray-500">Email:</span> {{ $staff->user->email ?? '—' }}</p>
            <p><span class="text-gray-500">Phone:</span> {{ $staff->phone ?? '—' }}</p>
            <p><span class="text-gray-500">Department:</span> {{ $staff->department->name ?? '—' }}</p>
            <p><span class="text-gray-500">Designation:</span> {{ $staff->designation->title ?? '—' }}</p>
            <p><span class="text-gray-500">Type:</span> {{ ucfirst(str_replace('_',' ',$staff->staff_type)) }}</p>
            <p><span class="text-gray-500">Joined:</span> {{ $staff->joining_date?->format('d M Y') ?? '—' }}</p>
        </div>
        <div class="mt-4 flex gap-2 justify-center">
            <a href="{{ route('staff.edit', $staff) }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-edit mr-1"></i>Edit</a>
            <a href="{{ route('staff.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg">Back</a>
        </div>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
        <h4 class="font-semibold text-gray-900 mb-3">Teaching Assignments</h4>
        @if($staff->teachingAssignments->count())
            <div class="space-y-2">
                @foreach($staff->teachingAssignments as $ta)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div><span class="font-mono text-blue-600 text-sm">{{ $ta->courseSection->course->code ?? '' }}</span> <span class="text-gray-900 text-sm">{{ $ta->courseSection->course->name ?? '' }}</span></div>
                        <span class="text-xs text-gray-500">Section {{ $ta->courseSection->section_name ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400">No teaching assignments.</p>
        @endif
    </div>
</div>
@endsection
