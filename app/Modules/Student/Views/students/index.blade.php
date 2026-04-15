@extends('core::layouts.app')
@section('title', 'Students')
@section('page_title', 'Students')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-gray-900">Students</h3><p class="text-sm text-gray-500">{{ $students->total() }} students enrolled</p></div>
        <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-plus"></i> Admit Student</a>
    </div>
    @if($students->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr><th class="px-5 py-3 text-left">Student</th><th class="px-5 py-3 text-left">ID</th><th class="px-5 py-3 text-left">Program</th><th class="px-5 py-3 text-left">Year</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-medium text-sm">{{ substr($student->user->name ?? 'S', 0, 1) }}</div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $student->user->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $student->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-gray-600">{{ $student->student_id }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $student->program->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">Year {{ $student->current_year }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs capitalize {{ $student->status === 'active' ? 'bg-green-100 text-green-700' : ($student->status === 'graduated' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700') }}">{{ $student->status }}</span></td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('students.show', $student) }}" class="text-gray-500 hover:text-gray-700 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('students.edit', $student) }}" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('students.destroy', $student) }}" class="inline" onsubmit="return confirm('Remove this student?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $students->links() }}</div>
    @else
    <div class="py-16 text-center"><i class="fas fa-user-graduate text-gray-300 text-4xl mb-4"></i><p class="text-gray-500">No students enrolled yet.</p>
        <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-plus"></i> Admit First Student</a></div>
    @endif
</div>
@endsection
