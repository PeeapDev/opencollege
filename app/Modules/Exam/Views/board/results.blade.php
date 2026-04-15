@extends('core::layouts.app')
@section('title', 'Result Publications')
@section('page_title', 'Result Publications')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-xl border p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-4">Publish Results</h3>
        <form method="POST" action="{{ route('exam.results.publish') }}" class="flex items-end gap-4">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Exam Type</label>
                <select name="exam_type_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                    <option value="">Select...</option>
                    @php $examTypes = \App\Modules\Exam\Models\ExamType::where('institution_id', auth()->user()->current_institution_id)->get(); @endphp
                    @foreach($examTypes as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                <select name="semester_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                    <option value="">Select...</option>
                    @php $semesters = \App\Modules\Academic\Models\Semester::where('institution_id', auth()->user()->current_institution_id)->get(); @endphp
                    @foreach($semesters as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex-shrink-0">
                <i class="fas fa-globe mr-1"></i> Publish
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Published</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($publications as $pub)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $pub->exam_type }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $pub->semester_name }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $pub->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $pub->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-xs text-gray-500">{{ $pub->published_at ? \Carbon\Carbon::parse($pub->published_at)->format('M d, Y h:i A') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">No publications yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $publications->links() }}</div>
</div>
@endsection
