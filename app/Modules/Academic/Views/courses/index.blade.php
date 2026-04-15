@extends('core::layouts.app')
@section('title', 'Courses')
@section('page_title', 'Courses')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-gray-900">Courses</h3><p class="text-sm text-gray-500">Manage course catalog</p></div>
        <a href="{{ route('courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-plus"></i> Add Course</a>
    </div>
    @if($courses->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr><th class="px-5 py-3 text-left">Code</th><th class="px-5 py-3 text-left">Name</th><th class="px-5 py-3 text-left">Department</th><th class="px-5 py-3 text-left">Credits</th><th class="px-5 py-3 text-left">Type</th><th class="px-5 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($courses as $course)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-blue-600 font-medium">{{ $course->code }}</td>
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $course->name }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $course->department->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $course->credit_hours }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs capitalize {{ $course->type === 'core' ? 'bg-blue-100 text-blue-700' : ($course->type === 'elective' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">{{ $course->type }}</span></td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('courses.edit', $course) }}" class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('courses.destroy', $course) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $courses->links() }}</div>
    @else
    <div class="py-16 text-center"><i class="fas fa-chalkboard text-gray-300 text-4xl mb-4"></i><p class="text-gray-500">No courses yet.</p>
        <a href="{{ route('courses.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-plus"></i> Add First</a></div>
    @endif
</div>
@endsection
