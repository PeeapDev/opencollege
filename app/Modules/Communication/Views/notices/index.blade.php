@extends('core::layouts.app')
@section('title', 'Notices')
@section('page_title', 'Notice Board')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-gray-500 text-sm">{{ $notices->total() }} notices</p>
    <a href="{{ route('notices.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i> New Notice
    </a>
</div>
<div class="space-y-4">
@forelse($notices as $notice)
    <div class="bg-white rounded-xl border p-5 hover:shadow-sm transition">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4 flex-1">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $notice->is_pinned ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600' }}">
                    <i class="fas {{ $notice->is_pinned ? 'fa-thumbtack' : 'fa-bullhorn' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-semibold text-gray-900">{{ $notice->title }}</h3>
                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-blue-100 text-blue-700">{{ ucfirst($notice->audience) }}</span>
                    </div>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit(strip_tags($notice->content), 200) }}</p>
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                        <span><i class="fas fa-calendar mr-1"></i>{{ $notice->publish_date->format('M d, Y') }}</span>
                        <span><i class="fas fa-user mr-1"></i>{{ $notice->creator->name ?? 'System' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('notices.edit', $notice) }}" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50"><i class="fas fa-edit text-sm"></i></a>
                <form method="POST" action="{{ route('notices.destroy', $notice) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                    <button class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50"><i class="fas fa-trash text-sm"></i></button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl border p-12 text-center">
        <i class="fas fa-bullhorn text-4xl text-gray-300 mb-4"></i>
        <p class="text-gray-500">No notices yet.</p>
    </div>
@endforelse
</div>
<div class="mt-6">{{ $notices->links() }}</div>
@endsection
