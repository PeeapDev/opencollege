@extends('core::layouts.app')
@section('title', 'Manage Colleges')
@section('page_title', 'Manage Colleges')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-gray-900">All Colleges</h3><p class="text-sm text-gray-500">{{ $colleges->total() }} institutions registered</p></div>
        <a href="{{ route('superadmin.colleges.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-plus"></i> Register College</a>
    </div>
    @if($colleges->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr><th class="px-5 py-3 text-left">College</th><th class="px-5 py-3 text-left">Domain</th><th class="px-5 py-3 text-left">Type</th><th class="px-5 py-3 text-center">Students</th><th class="px-5 py-3 text-center">Staff</th><th class="px-5 py-3 text-left">Plan</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-left">Registered</th><th class="px-5 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($colleges as $col)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg {{ $col->active ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center font-bold text-sm">{{ strtoupper(substr($col->code, 0, 2)) }}</div>
                            <div><p class="font-medium text-gray-900">{{ $col->name }}</p><p class="text-[11px] text-gray-400">{{ $col->email }}</p></div>
                        </div>
                    </td>
                    <td class="px-5 py-3"><span class="font-mono text-blue-600 text-xs">{{ $col->domain }}.college.edu.sl</span></td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs capitalize">{{ $col->type ?? 'college' }}</span></td>
                    <td class="px-5 py-3 text-center font-medium">{{ $col->students_count ?? 0 }}</td>
                    <td class="px-5 py-3 text-center font-medium">{{ $col->staff_members_count ?? 0 }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs capitalize {{ $col->plan === 'premium' ? 'bg-purple-100 text-purple-700' : ($col->plan === 'basic' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">{{ $col->plan ?? 'free' }}</span></td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $col->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $col->active ? 'Active' : 'Disabled' }}</span></td>
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $col->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('superadmin.colleges.toggle', $col) }}" class="inline">@csrf
                                <button class="text-xs px-2.5 py-1 rounded-md {{ $col->active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} transition">{{ $col->active ? 'Disable' : 'Enable' }}</button>
                            </form>
                            <form method="POST" action="{{ route('superadmin.colleges.destroy', $col) }}" class="inline" onsubmit="return confirm('Permanently delete {{ $col->name }}? This cannot be undone.')">@csrf @method('DELETE')
                                <button class="text-xs px-2.5 py-1 rounded-md bg-gray-50 text-red-500 hover:bg-red-50 transition"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $colleges->links() }}</div>
    @else
    <div class="py-16 text-center"><i class="fas fa-school text-gray-300 text-4xl mb-4"></i><p class="text-gray-500">No colleges registered yet.</p>
        <a href="{{ route('superadmin.colleges.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-plus"></i> Register First College</a></div>
    @endif
</div>
@endsection
