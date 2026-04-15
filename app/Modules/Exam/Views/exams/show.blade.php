@extends('core::layouts.app')
@section('title', 'Exam Details')
@section('page_title', 'Exam Details')
@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $exam->courseSection->course->name ?? 'Exam' }} — {{ $exam->examType->name ?? '' }}</h3>
        <div class="grid grid-cols-3 gap-4 text-sm mb-6">
            <div class="bg-gray-50 rounded-lg p-3"><p class="text-gray-500">Date</p><p class="font-medium">{{ $exam->exam_date?->format('d M Y') ?? 'TBD' }}</p></div>
            <div class="bg-gray-50 rounded-lg p-3"><p class="text-gray-500">Total Marks</p><p class="font-medium">{{ $exam->total_marks }}</p></div>
            <div class="bg-gray-50 rounded-lg p-3"><p class="text-gray-500">Pass Marks</p><p class="font-medium">{{ $exam->pass_marks }}</p></div>
        </div>
        @if($exam->grades->count())
        <h4 class="font-semibold text-gray-900 mb-2">Grades</h4>
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left">Student</th><th class="px-4 py-2 text-left">Marks</th><th class="px-4 py-2 text-left">Grade</th><th class="px-4 py-2 text-left">GPA</th></tr></thead>
            <tbody class="divide-y">
                @foreach($exam->grades as $g)
                <tr><td class="px-4 py-2">{{ $g->student->user->name ?? '—' }}</td><td class="px-4 py-2">{{ $g->marks_obtained }}</td><td class="px-4 py-2 font-bold">{{ $g->letter_grade ?? '—' }}</td><td class="px-4 py-2">{{ $g->grade_point ?? '—' }}</td></tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-sm text-gray-400">No grades entered yet.</p>
        @endif
        <div class="mt-4"><a href="{{ route('exams.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg">Back</a></div>
    </div>
</div>
@endsection
