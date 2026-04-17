@extends('core::layouts.app')
@section('title', 'Students')
@section('page_title', 'Students')

@section('content')

<div class="bg-white rounded-xl border border-slate-200 mb-4" x-data="{ view: localStorage.getItem('studentListView') || 'grid' }">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-slate-900">Students</h3>
            <p class="text-sm text-slate-500">{{ $students->total() }} students enrolled</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="inline-flex rounded-lg border border-slate-200 overflow-hidden">
                <button type="button" @click="view='grid'; localStorage.setItem('studentListView','grid')"
                        :class="view==='grid' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600'"
                        class="px-3 py-1.5 text-xs font-medium transition" title="Grid view">
                    <i class="fas fa-th"></i>
                </button>
                <button type="button" @click="view='table'; localStorage.setItem('studentListView','table')"
                        :class="view==='table' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600'"
                        class="px-3 py-1.5 text-xs font-medium transition" title="Table view">
                    <i class="fas fa-list"></i>
                </button>
            </div>
            <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <i class="fas fa-plus"></i> Admit Student
            </a>
        </div>
    </div>

    @if($students->count())
        {{-- GRID VIEW --}}
        <div x-show="view==='grid'" class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($students as $student)
                    @php
                        $photoUrl = $student->photo ? asset($student->photo) : null;
                        $name = $student->user->name ?? 'Unknown';
                        $initials = strtoupper(substr($name, 0, 1));
                    @endphp
                    <a href="{{ route('students.show', $student) }}"
                       class="group bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-lg hover:border-blue-400 hover:-translate-y-0.5 transition">
                        <div class="aspect-square bg-gradient-to-br from-slate-100 to-slate-200 relative">
                            @if ($photoUrl)
                                <img src="{{ $photoUrl }}" alt="{{ $name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600">
                                    <span class="text-white text-4xl font-bold">{{ $initials }}</span>
                                </div>
                            @endif
                            <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded-full text-[10px] uppercase tracking-wide font-semibold {{ $student->status === 'active' ? 'bg-emerald-500 text-white' : 'bg-slate-500 text-white' }}">
                                {{ $student->status }}
                            </span>
                        </div>
                        <div class="p-3 text-center">
                            <div class="font-semibold text-slate-900 text-sm leading-tight truncate" title="{{ $name }}">{{ $name }}</div>
                            <div class="text-xs font-mono text-slate-500 mt-0.5 truncate">{{ $student->student_id }}</div>
                            @if($student->nsi_number)
                                <div class="text-[10px] font-mono text-blue-600 mt-0.5 truncate" title="NSI {{ $student->nsi_number }}">{{ $student->nsi_number }}</div>
                            @endif
                            <div class="text-xs text-slate-500 mt-1 truncate" title="{{ $student->program->name ?? '' }}">{{ $student->program->name ?? '—' }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- TABLE VIEW --}}
        <div x-show="view==='table'" x-cloak class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Student</th>
                        <th class="px-5 py-3 text-left">Matric</th>
                        <th class="px-5 py-3 text-left">NSI</th>
                        <th class="px-5 py-3 text-left">Program</th>
                        <th class="px-5 py-3 text-left">Year</th>
                        <th class="px-5 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($students as $student)
                        @php
                            $photoUrl = $student->photo ? asset($student->photo) : null;
                            $name = $student->user->name ?? 'Unknown';
                            $initials = strtoupper(substr($name, 0, 1));
                        @endphp
                        <tr class="hover:bg-slate-50 cursor-pointer" onclick="window.location='{{ route('students.show', $student) }}'">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="{{ $name }}" class="w-9 h-9 rounded-full object-cover">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $name }}</p>
                                        <p class="text-xs text-slate-500">{{ $student->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-700">{{ $student->student_id }}</td>
                            <td class="px-5 py-3 font-mono text-xs text-blue-700">{{ $student->nsi_number ?: '—' }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $student->program->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-600">Year {{ $student->current_year ?? 1 }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs capitalize {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $student->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $students->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <i class="fas fa-user-graduate text-5xl text-slate-300 mb-3"></i>
            <p class="text-slate-500">No students yet.</p>
            <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg">
                <i class="fas fa-plus"></i> Admit the first student
            </a>
        </div>
    @endif
</div>
@endsection
