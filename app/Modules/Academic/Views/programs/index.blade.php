@extends('core::layouts.app')
@section('title', 'Programs')
@section('page_title', 'Programs')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-gray-900">Programs</h3><p class="text-sm text-gray-500">Degree programs and certificates</p></div>
        <a href="{{ route('programs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-plus"></i> Add Program</a>
    </div>
    @if($programs->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr><th class="px-5 py-3 text-left">Name</th><th class="px-5 py-3 text-left">Code</th><th class="px-5 py-3 text-left">Department</th><th class="px-5 py-3 text-left">Level</th><th class="px-5 py-3 text-left">Duration</th><th class="px-5 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($programs as $prog)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $prog->name }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $prog->code ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $prog->department->name ?? '—' }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs capitalize">{{ str_replace('_', ' ', $prog->level) }}</span></td>
                    <td class="px-5 py-3 text-gray-500">{{ $prog->duration_years }} yr{{ $prog->duration_years > 1 ? 's' : '' }}</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('programs.edit', $prog) }}" class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('programs.destroy', $prog) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $programs->links() }}</div>
    @else
    <div class="py-16 text-center"><i class="fas fa-book-open text-gray-300 text-4xl mb-4"></i><p class="text-gray-500">No programs yet.</p>
        <a href="{{ route('programs.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-plus"></i> Add First</a></div>
    @endif
</div>
@endsection
