@extends('core::layouts.app')
@section('title', 'Student Portal')
@section('page_title', 'My Dashboard')

@section('content')
<div class="mb-6">
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold">
                {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold">Welcome, {{ $student->user->name ?? 'Student' }}</h2>
                <p class="text-blue-200 text-sm">{{ $student->program->name ?? '' }} • Year {{ $student->current_year }} • Semester {{ $student->current_semester }}</p>
                <p class="text-blue-300 text-xs mt-1">Matric: {{ $student->student_id }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-chart-line text-blue-600"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ number_format($gpa, 2) }}</p><p class="text-xs text-gray-500">Current GPA</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-book text-green-600"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $totalCredits }}</p><p class="text-xs text-gray-500">Credits Earned</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-file-invoice text-amber-600"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ number_format($outstanding, 0) }}</p><p class="text-xs text-gray-500">Outstanding (Le)</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-graduation-cap text-purple-600"></i></div>
            <div><p class="text-sm font-semibold text-gray-900">{{ ucfirst($student->status) }}</p><p class="text-xs text-gray-500">Status</p></div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-award text-blue-500"></i> Recent Grades</h3>
        @if($grades->count())
        <div class="space-y-3">
            @foreach($grades as $grade)
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $grade->course->name ?? 'Course' }}</p>
                    <p class="text-xs text-gray-400">{{ $grade->examType->name ?? '' }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs font-bold rounded {{ in_array($grade->letter_grade, ['A+','A','A-','B+','B']) ? 'bg-green-100 text-green-700' : (in_array($grade->letter_grade, ['B-','C+','C']) ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ $grade->letter_grade }}</span>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $grade->score }}%</p>
                </div>
            </div>
            @endforeach
        </div>
        <a href="{{ route('student.results') }}" class="text-blue-600 text-sm hover:underline mt-3 inline-block">View All Results →</a>
        @else
        <p class="text-sm text-gray-400 py-4 text-center">No grades yet</p>
        @endif
    </div>

    <div class="bg-white rounded-xl border p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-bullhorn text-amber-500"></i> Notices</h3>
        @if($notices->count())
        <div class="space-y-3">
            @foreach($notices as $notice)
            <div class="py-2 border-b last:border-0">
                <p class="text-sm font-medium text-gray-900">{{ $notice->title }}</p>
                <p class="text-xs text-gray-400">{{ $notice->publish_date->format('M d, Y') }}</p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-400 py-4 text-center">No notices</p>
        @endif
    </div>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
    <a href="{{ route('student.profile') }}" class="bg-white rounded-xl border p-5 hover:shadow-md transition text-center">
        <i class="fas fa-user-circle text-blue-500 text-2xl mb-2"></i>
        <p class="text-sm font-medium text-gray-700">My Profile</p>
    </a>
    <a href="{{ route('student.results') }}" class="bg-white rounded-xl border p-5 hover:shadow-md transition text-center">
        <i class="fas fa-poll text-green-500 text-2xl mb-2"></i>
        <p class="text-sm font-medium text-gray-700">My Results</p>
    </a>
    <a href="{{ route('student.finances') }}" class="bg-white rounded-xl border p-5 hover:shadow-md transition text-center">
        <i class="fas fa-wallet text-amber-500 text-2xl mb-2"></i>
        <p class="text-sm font-medium text-gray-700">Finances</p>
    </a>
    <a href="{{ route('student.id_card') }}" class="bg-white rounded-xl border p-5 hover:shadow-md transition text-center">
        <i class="fas fa-id-card text-purple-500 text-2xl mb-2"></i>
        <p class="text-sm font-medium text-gray-700">My ID Card</p>
    </a>
</div>
@endsection
