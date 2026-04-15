@extends('core::layouts.app')
@section('title', 'Exams & Grades')
@section('page_title', 'Exams & Grades')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-gray-900">Exams & Grades</h3><p class="text-sm text-gray-500">Manage exams and student grades</p></div>
        <a href="{{ route('exams.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-plus"></i> Schedule Exam</a>
    </div>
    @if($exams->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr><th class="px-5 py-3 text-left">Course</th><th class="px-5 py-3 text-left">Type</th><th class="px-5 py-3 text-left">Date</th><th class="px-5 py-3 text-left">Venue</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($exams as $exam)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $exam->courseSection->course->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $exam->examType->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $exam->exam_date?->format('d M Y') ?? 'TBD' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $exam->venue ?? '—' }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $exam->published ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $exam->published ? 'Published' : 'Draft' }}</span></td>
                    <td class="px-5 py-3 text-right"><a href="{{ route('exams.show', $exam) }}" class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $exams->links() }}</div>
    @else
    <div class="py-16 text-center"><i class="fas fa-clipboard-list text-gray-300 text-4xl mb-4"></i><p class="text-gray-500">No exams scheduled yet.</p></div>
    @endif
</div>
@endsection
