@extends('core::layouts.app')
@section('title', 'Faculties')
@section('page_title', 'Faculties')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Faculties</h3>
            <p class="text-sm text-gray-500">Manage academic faculties</p>
        </div>
        <a href="{{ route('faculties.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            <i class="fas fa-plus"></i> Add Faculty
        </a>
    </div>
    @if($faculties->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Code</th>
                    <th class="px-5 py-3 text-left">Dean</th>
                    <th class="px-5 py-3 text-left">Departments</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($faculties as $faculty)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $faculty->name }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $faculty->code ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $faculty->dean->name ?? '—' }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">{{ $faculty->departments->count() }}</span></td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $faculty->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $faculty->active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('faculties.edit', $faculty) }}" class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('faculties.destroy', $faculty) }}" class="inline" onsubmit="return confirm('Delete this faculty?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $faculties->links() }}</div>
    @else
    <div class="py-16 text-center">
        <i class="fas fa-university text-gray-300 text-4xl mb-4"></i>
        <p class="text-gray-500">No faculties yet.</p>
        <a href="{{ route('faculties.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-plus"></i> Add First Faculty</a>
    </div>
    @endif
</div>
@endsection
