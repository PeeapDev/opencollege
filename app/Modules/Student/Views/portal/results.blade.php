@extends('core::layouts.app')
@section('title', 'My Results')
@section('page_title', 'Academic Results')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-xl border p-5 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-900">{{ $student->user->name ?? '' }}</h3>
                <p class="text-sm text-gray-500">{{ $student->student_id }} • {{ $student->program->name ?? '' }}</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-blue-600">{{ number_format($gpa, 2) }}</p>
                <p class="text-xs text-gray-500">Cumulative GPA</p>
            </div>
        </div>
    </div>

    @forelse($grades as $groupName => $groupGrades)
    <div class="bg-white rounded-xl border mb-4 overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b">
            <h3 class="font-semibold text-sm text-gray-700">{{ $groupName }}</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="px-5 py-2 text-left text-xs font-medium text-gray-500">Course</th>
                    <th class="px-5 py-2 text-center text-xs font-medium text-gray-500">Credits</th>
                    <th class="px-5 py-2 text-center text-xs font-medium text-gray-500">Score</th>
                    <th class="px-5 py-2 text-center text-xs font-medium text-gray-500">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($groupGrades as $grade)
                <tr>
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-900">{{ $grade->course->name ?? 'Course' }}</p>
                        <p class="text-xs text-gray-400">{{ $grade->course->code ?? '' }}</p>
                    </td>
                    <td class="px-5 py-3 text-center text-gray-600">{{ $grade->credit_hours ?? 3 }}</td>
                    <td class="px-5 py-3 text-center text-gray-600">{{ $grade->score }}%</td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-1 text-xs font-bold rounded {{ in_array($grade->letter_grade, ['A+','A','A-','B+','B']) ? 'bg-green-100 text-green-700' : (in_array($grade->letter_grade, ['B-','C+','C']) ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ $grade->letter_grade }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <div class="bg-white rounded-xl border p-12 text-center">
        <i class="fas fa-poll text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">No results published yet</p>
    </div>
    @endforelse
</div>
@endsection
