@extends('core::layouts.app')
@section('title', 'Staff Directory')
@section('page_title', 'Staff Directory')

@section('content')
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Staff</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-green-600">{{ $stats['academic'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Academic</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-purple-600">{{ $stats['non_academic'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Non-Academic</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-amber-600">{{ $stats['admin'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Admin</p>
    </div>
</div>

<div class="bg-white rounded-xl border">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">All Staff Members</h3>
        <a href="{{ route('hr.dashboard') }}" class="text-xs text-blue-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i>HR Dashboard</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-5 py-2.5 text-left">Name</th>
                    <th class="px-5 py-2.5 text-left">Email</th>
                    <th class="px-5 py-2.5 text-left">Department</th>
                    <th class="px-5 py-2.5 text-left">Designation</th>
                    <th class="px-5 py-2.5 text-left">Type</th>
                    <th class="px-5 py-2.5 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($staff as $s)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xs">
                                {{ strtoupper(substr($s->user->name ?? 'S', 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-900">{{ $s->user->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $s->user->email ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $s->department->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $s->designation->title ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ ($s->staff_type ?? '') === 'academic' ? 'bg-green-50 text-green-700' : '' }}
                            {{ ($s->staff_type ?? '') === 'non_academic' ? 'bg-purple-50 text-purple-700' : '' }}
                            {{ ($s->staff_type ?? '') === 'admin' ? 'bg-amber-50 text-amber-700' : '' }}
                        ">{{ ucfirst(str_replace('_', ' ', $s->staff_type ?? 'staff')) }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ ($s->status ?? '') === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($s->status ?? 'active') }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No staff members found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $staff->links() }}</div>
</div>
@endsection
