@extends('hemis::layout')
@section('title', 'Students')
@section('page_title', 'National Student Registry')
@section('subtitle', 'Search students across every higher-education institution in Sierra Leone')

@section('content')

<div class="bg-white rounded-xl border border-slate-200 p-4 mb-4">
    <form method="GET" action="{{ route('hemis.students') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Search by NSI, matric, name, or email"
               class="md:col-span-2 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="institution" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
            <option value="">All institutions</option>
            @foreach($institutions as $inst)
                <option value="{{ $inst->id }}" @selected(($institutionId ?? '') == $inst->id)>{{ $inst->name }} ({{ $inst->code }})</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 whitespace-nowrap">
                <i class="fas fa-search mr-1"></i> Search
            </button>
            @if($search || $institutionId)
                <a href="{{ route('hemis.students') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm hover:bg-slate-50">Clear</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl border border-slate-200">
    @if ($students->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-user-graduate text-5xl text-slate-300 mb-3"></i>
            <p class="text-slate-500">
                @if ($search || $institutionId) No students match your search. @else Enter a query above to search the national registry. @endif
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                    <tr>
                        <th class="text-left px-6 py-3">Student</th>
                        <th class="text-left px-6 py-3">NSI</th>
                        <th class="text-left px-6 py-3">Matric</th>
                        <th class="text-left px-6 py-3">Institution</th>
                        <th class="text-left px-6 py-3">Program</th>
                        <th class="text-left px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($students as $s)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3">
                                <a href="{{ route('hemis.students.show', $s->nsi_number ?: $s->student_id) }}" class="font-medium text-slate-900 hover:text-blue-600">
                                    {{ $s->user->name ?? 'Unknown' }}
                                </a>
                                <div class="text-xs text-slate-500">{{ $s->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-3 font-mono text-xs text-slate-700">{{ $s->nsi_number ?: '—' }}</td>
                            <td class="px-6 py-3 font-mono text-xs text-slate-700">{{ $s->student_id }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $s->institution->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $s->program->name ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-block px-2 py-0.5 text-xs rounded-full capitalize
                                    {{ $s->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $s->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-slate-200">
            {{ $students->links() }}
        </div>
    @endif
</div>

@endsection
