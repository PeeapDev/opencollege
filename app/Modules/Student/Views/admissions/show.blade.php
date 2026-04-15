@extends('core::layouts.app')
@section('title', 'Application Details')
@section('page_title', 'Application: ' . $admission->application_number)

@section('content')
<div class="max-w-3xl">
    <div class="mb-4"><a href="{{ route('admissions.index') }}" class="text-sm text-blue-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i> Back to Admissions</a></div>

    <div class="bg-white rounded-xl border p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $admission->first_name }} {{ $admission->middle_name }} {{ $admission->last_name }}</h2>
                <p class="text-sm text-gray-500">{{ $admission->application_number }}</p>
            </div>
            @php $colors = ['pending'=>'amber','accepted'=>'green','rejected'=>'red','enrolled'=>'blue']; $c = $colors[$admission->status] ?? 'gray'; @endphp
            <span class="px-3 py-1.5 text-sm font-medium rounded-full bg-{{ $c }}-100 text-{{ $c }}-700">{{ ucfirst(str_replace('_',' ',$admission->status)) }}</span>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <h3 class="text-sm font-semibold text-gray-700 border-b pb-1">Personal Information</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-400">Email</span><p class="text-gray-900">{{ $admission->email }}</p></div>
                    <div><span class="text-gray-400">Phone</span><p class="text-gray-900">{{ $admission->phone ?? '—' }}</p></div>
                    <div><span class="text-gray-400">DOB</span><p class="text-gray-900">{{ $admission->date_of_birth?->format('M d, Y') ?? '—' }}</p></div>
                    <div><span class="text-gray-400">Gender</span><p class="text-gray-900">{{ ucfirst($admission->gender ?? '—') }}</p></div>
                    <div><span class="text-gray-400">Nationality</span><p class="text-gray-900">{{ $admission->nationality }}</p></div>
                    <div><span class="text-gray-400">NSI</span><p class="text-gray-900">{{ $admission->nsi_number ?? '—' }}</p></div>
                </div>
                <div class="text-sm"><span class="text-gray-400">Address</span><p class="text-gray-900">{{ $admission->address ?? '—' }}, {{ $admission->city ?? '' }}</p></div>
            </div>
            <div class="space-y-3">
                <h3 class="text-sm font-semibold text-gray-700 border-b pb-1">Academic</h3>
                <div class="text-sm">
                    <span class="text-gray-400">Program</span><p class="text-gray-900 font-medium">{{ $admission->program->name ?? '—' }}</p>
                </div>
                <h3 class="text-sm font-semibold text-gray-700 border-b pb-1 mt-4">Guardian</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-400">Name</span><p class="text-gray-900">{{ $admission->guardian_name ?? '—' }}</p></div>
                    <div><span class="text-gray-400">Phone</span><p class="text-gray-900">{{ $admission->guardian_phone ?? '—' }}</p></div>
                    <div><span class="text-gray-400">Email</span><p class="text-gray-900">{{ $admission->guardian_email ?? '—' }}</p></div>
                    <div><span class="text-gray-400">Relation</span><p class="text-gray-900">{{ $admission->guardian_relation ?? '—' }}</p></div>
                </div>
            </div>
        </div>

        @if($admission->reviewer)
        <div class="mt-6 pt-4 border-t text-sm text-gray-500">
            Reviewed by {{ $admission->reviewer->name }} on {{ $admission->reviewed_at?->format('M d, Y') }}
            @if($admission->admin_notes)<p class="mt-1 text-gray-600">Notes: {{ $admission->admin_notes }}</p>@endif
        </div>
        @endif
    </div>

    @if($admission->status === 'pending')
    <div class="flex items-center gap-3">
        <form method="POST" action="{{ route('admissions.accept', $admission) }}">@csrf
            <button class="px-6 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition"><i class="fas fa-check mr-1"></i> Accept</button>
        </form>
        <form method="POST" action="{{ route('admissions.reject', $admission) }}" onsubmit="return confirm('Reject?')">@csrf
            <button class="px-6 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition"><i class="fas fa-times mr-1"></i> Reject</button>
        </form>
    </div>
    @elseif($admission->status === 'accepted')
    <form method="POST" action="{{ route('admissions.enroll', $admission) }}" onsubmit="return confirm('Enroll this student? Default password: student123')">@csrf
        <button class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition"><i class="fas fa-user-plus mr-1"></i> Enroll as Student</button>
    </form>
    @endif
</div>
@endsection
