@extends('core::layouts.app')
@section('title', 'Schedule Exam')
@section('page_title', 'Schedule Exam')
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="py-8 text-center text-gray-400"><i class="fas fa-clipboard-list text-4xl mb-3"></i><p>Exam scheduling form coming soon.</p><p class="text-sm mt-1">Set up courses and exam types first.</p></div>
        <a href="{{ route('exams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg">Back</a>
    </div>
</div>
@endsection
