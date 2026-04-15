@extends('core::layouts.app')
@section('title', 'Admissions')
@section('page_title', 'Admission Management')

@section('content')
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center">
        <div class="text-2xl font-bold text-amber-600">{{ $counts['pending'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Pending</div>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <div class="text-2xl font-bold text-green-600">{{ $counts['accepted'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Accepted</div>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <div class="text-2xl font-bold text-red-600">{{ $counts['rejected'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Rejected</div>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $counts['enrolled'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Enrolled</div>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">App #</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($admissions as $app)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $app->application_number }}</td>
                <td class="px-4 py-3">
                    <div class="font-medium text-gray-900">{{ $app->first_name }} {{ $app->last_name }}</div>
                    <div class="text-xs text-gray-400">{{ $app->email }}</div>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $app->program->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $app->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    @php $colors = ['pending'=>'amber','accepted'=>'green','rejected'=>'red','enrolled'=>'blue','under_review'=>'purple','waitlisted'=>'orange']; $c = $colors[$app->status] ?? 'gray'; @endphp
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $c }}-100 text-{{ $c }}-700">{{ ucfirst(str_replace('_',' ',$app->status)) }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('admissions.show', $app) }}" class="p-1.5 text-gray-400 hover:text-blue-600 rounded" title="View"><i class="fas fa-eye text-xs"></i></a>
                        @if($app->status === 'pending')
                        <form method="POST" action="{{ route('admissions.accept', $app) }}" class="inline">@csrf
                            <button class="p-1.5 text-gray-400 hover:text-green-600 rounded" title="Accept"><i class="fas fa-check text-xs"></i></button>
                        </form>
                        <form method="POST" action="{{ route('admissions.reject', $app) }}" class="inline" onsubmit="return confirm('Reject this application?')">@csrf
                            <button class="p-1.5 text-gray-400 hover:text-red-600 rounded" title="Reject"><i class="fas fa-times text-xs"></i></button>
                        </form>
                        @endif
                        @if($app->status === 'accepted')
                        <form method="POST" action="{{ route('admissions.enroll', $app) }}" class="inline" onsubmit="return confirm('Enroll this student?')">@csrf
                            <button class="p-1.5 text-gray-400 hover:text-blue-600 rounded" title="Enroll"><i class="fas fa-user-plus text-xs"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No applications yet</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $admissions->links() }}</div>
@endsection
