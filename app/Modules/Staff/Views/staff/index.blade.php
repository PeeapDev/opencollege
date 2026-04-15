@extends('core::layouts.app')
@section('title', 'Staff')
@section('page_title', 'Staff')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-gray-900">Staff</h3><p class="text-sm text-gray-500">{{ $staff->total() }} staff members</p></div>
        <a href="{{ route('staff.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-plus"></i> Add Staff</a>
    </div>
    @if($staff->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr><th class="px-5 py-3 text-left">Name</th><th class="px-5 py-3 text-left">Staff ID</th><th class="px-5 py-3 text-left">Department</th><th class="px-5 py-3 text-left">Designation</th><th class="px-5 py-3 text-left">Type</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($staff as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-medium text-sm">{{ substr($member->user->name ?? 'S', 0, 1) }}</div>
                            <div><p class="font-medium text-gray-900">{{ $member->user->name ?? 'Unknown' }}</p><p class="text-xs text-gray-500">{{ $member->user->email ?? '' }}</p></div>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-gray-600">{{ $member->staff_id }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $member->department->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $member->designation->title ?? '—' }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs capitalize {{ $member->staff_type === 'academic' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">{{ str_replace('_', ' ', $member->staff_type) }}</span></td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs capitalize {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $member->status }}</span></td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('staff.edit', $member) }}" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('staff.destroy', $member) }}" class="inline" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $staff->links() }}</div>
    @else
    <div class="py-16 text-center"><i class="fas fa-users text-gray-300 text-4xl mb-4"></i><p class="text-gray-500">No staff yet.</p>
        <a href="{{ route('staff.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-plus"></i> Add First</a></div>
    @endif
</div>
@endsection
