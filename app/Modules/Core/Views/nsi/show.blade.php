@extends('core::layouts.app')
@section('title', 'Academic History — ' . ($verification->nsi_number ?? ''))
@section('page_title', 'NSI Academic History')

@section('content')
<div class="mb-4"><a href="{{ route('nsi.index') }}" class="text-sm text-blue-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i> Back to Verifications</a></div>

{{-- Student Info Card --}}
<div class="bg-white rounded-xl border p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            @if($apiResponse['photo_url'] ?? null)
            <img src="{{ $apiResponse['photo_url'] }}" class="w-16 h-16 rounded-full object-cover border-2 border-blue-200">
            @else
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl">
                {{ strtoupper(substr($apiResponse['first_name'] ?? 'S', 0, 1)) }}
            </div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $apiResponse['student_name'] ?? $verification->student_name }}</h2>
                <p class="text-sm text-gray-500">NSI: <span class="font-mono font-medium">{{ $verification->nsi_number }}</span></p>
                <div class="flex flex-wrap gap-3 mt-1 text-xs text-gray-400">
                    @if($apiResponse['school_name'] ?? null)<span><i class="fas fa-school mr-1"></i>{{ $apiResponse['school_name'] }}</span>@endif
                    @if($apiResponse['current_class'] ?? null)<span><i class="fas fa-chalkboard mr-1"></i>{{ $apiResponse['current_class'] }}</span>@endif
                    @if($apiResponse['gender'] ?? null)<span><i class="fas fa-user mr-1"></i>{{ $apiResponse['gender'] }}</span>@endif
                    @if($apiResponse['date_of_birth'] ?? null)<span><i class="fas fa-birthday-cake mr-1"></i>{{ $apiResponse['date_of_birth'] }}</span>@endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @if($apiResponse['aggregate_score'] ?? null)
            <div class="text-center px-4 py-2 bg-blue-50 rounded-lg">
                <p class="text-2xl font-bold text-blue-600">{{ $apiResponse['aggregate_score'] }}%</p>
                <p class="text-[10px] text-blue-500 uppercase font-medium">Aggregate</p>
            </div>
            @endif
            <div class="text-center px-4 py-2 bg-green-50 rounded-lg">
                <p class="text-2xl font-bold text-green-600">{{ $apiResponse['total_classes_attended'] ?? count($academicHistory) }}</p>
                <p class="text-[10px] text-green-500 uppercase font-medium">Classes</p>
            </div>
        </div>
    </div>
</div>

{{-- External Exam Classes Legend --}}
@if(!empty($examClasses))
<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
    <h3 class="text-sm font-semibold text-amber-800 mb-2"><i class="fas fa-star mr-1"></i> External Exam Milestones</h3>
    <div class="grid sm:grid-cols-3 gap-3">
        @foreach($examClasses as $key => $ec)
        <div class="flex items-center gap-2 text-xs">
            <span class="w-3 h-3 rounded-full {{ $key === 'class_6' ? 'bg-purple-500' : ($key === 'jss3' ? 'bg-orange-500' : 'bg-red-500') }}"></span>
            <div>
                <span class="font-medium text-gray-800">{{ $ec['label'] }}</span>
                <span class="text-gray-500 ml-1">{{ $ec['description'] }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Academic Journey Timeline --}}
<div class="space-y-4">
@forelse($academicHistory as $idx => $history)
    @php
        $borderColor = 'border-gray-200';
        $badge = null;
        if ($history['is_exam_class']) {
            $colors = ['class_6' => ['border-purple-400', 'bg-purple-100 text-purple-700', 'NPSE'], 'jss3' => ['border-orange-400', 'bg-orange-100 text-orange-700', 'BECE'], 'sss3' => ['border-red-400', 'bg-red-100 text-red-700', 'WASSCE']];
            $c = $colors[$history['exam_class_type']] ?? ['border-gray-400', 'bg-gray-100 text-gray-700', 'EXAM'];
            $borderColor = $c[0];
            $badge = [$c[1], $c[2]];
        }
    @endphp
    <div class="bg-white rounded-xl border-2 {{ $borderColor }} overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 {{ $history['is_exam_class'] ? 'bg-amber-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center">
                    <i class="fas {{ $history['is_exam_class'] ? 'fa-star text-amber-600' : 'fa-graduation-cap text-blue-600' }} text-sm"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $history['class'] }}</h3>
                    <p class="text-xs text-gray-400">
                        {{ $history['school_name'] ?? '' }}
                        @if($history['academic_year']) • {{ $history['academic_year'] }} @endif
                        @if($history['section']) • Section {{ $history['section'] }} @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($badge)
                <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $badge[0] }}">{{ $badge[1] }}</span>
                @endif
                @if($history['is_promoted'])
                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700"><i class="fas fa-arrow-up mr-0.5"></i>Promoted</span>
                @endif
            </div>
        </div>

        @if($history['has_results'])
            @foreach($history['results'] as $examResult)
            <div class="px-5 py-3 {{ !$loop->last ? 'border-b' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-700"><i class="fas fa-clipboard-list text-gray-400 mr-1"></i> {{ $examResult['exam_type'] }}</h4>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="text-gray-400">{{ $examResult['total_subjects'] }} subjects</span>
                        <span class="px-2 py-0.5 font-bold rounded {{ $examResult['average'] >= 70 ? 'bg-green-100 text-green-700' : ($examResult['average'] >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                            Avg: {{ $examResult['average'] }}%
                        </span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b">
                                <th class="pb-1 text-left text-gray-500 font-medium">Subject</th>
                                <th class="pb-1 text-center text-gray-500 font-medium w-16">Marks</th>
                                <th class="pb-1 text-center text-gray-500 font-medium w-14">Grade</th>
                                <th class="pb-1 text-center text-gray-500 font-medium w-14">GPA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examResult['subjects'] as $subj)
                            <tr class="border-b last:border-0">
                                <td class="py-1.5 text-gray-800">
                                    {{ $subj['subject'] }}
                                    @if($subj['absent'])<span class="ml-1 px-1 py-0.5 text-[9px] bg-red-100 text-red-600 rounded">ABSENT</span>@endif
                                </td>
                                <td class="py-1.5 text-center font-mono {{ $subj['marks'] >= 70 ? 'text-green-600' : ($subj['marks'] >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $subj['marks'] }}</td>
                                <td class="py-1.5 text-center">
                                    <span class="px-1.5 py-0.5 font-bold rounded {{ in_array($subj['grade'], ['A','A+']) ? 'bg-green-100 text-green-700' : (in_array($subj['grade'], ['B','B+']) ? 'bg-blue-100 text-blue-700' : ($subj['grade'] === 'C' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')) }}">{{ $subj['grade'] }}</span>
                                </td>
                                <td class="py-1.5 text-center text-gray-500">{{ $subj['gpa'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        @else
            <div class="px-5 py-6 text-center text-gray-400 text-sm">
                <i class="fas fa-file-alt text-2xl mb-2"></i>
                <p>No exam results recorded for this class</p>
            </div>
        @endif
    </div>
@empty
    <div class="bg-white rounded-xl border p-12 text-center">
        <i class="fas fa-history text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">No academic history found for this NSI number</p>
    </div>
@endforelse
</div>

{{-- Verification metadata --}}
<div class="mt-6 bg-gray-50 rounded-xl border p-4 text-xs text-gray-400">
    <div class="flex flex-wrap gap-4">
        <span><i class="fas fa-clock mr-1"></i> Verified: {{ \Carbon\Carbon::parse($verification->created_at)->format('M d, Y h:i A') }}</span>
        <span><i class="fas fa-fingerprint mr-1"></i> Status: {{ ucfirst($verification->verification_status) }}</span>
        @if($verification->verified_by)<span><i class="fas fa-user mr-1"></i> By: User #{{ $verification->verified_by }}</span>@endif
    </div>
</div>
@endsection
