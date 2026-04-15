@extends('core::layouts.app')
@section('title', 'Sent Messages')
@section('page_title', 'Messages — Sent')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('messages.inbox') }}" class="px-3 py-1.5 text-sm text-gray-500 hover:bg-gray-100 rounded-lg">Inbox</a>
        <a href="{{ route('messages.sent') }}" class="px-3 py-1.5 text-sm font-medium bg-blue-100 text-blue-700 rounded-lg">Sent</a>
    </div>
    <a href="{{ route('messages.compose') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <i class="fas fa-pen"></i> Compose
    </a>
</div>
<div class="bg-white rounded-xl border divide-y">
@forelse($messages as $msg)
    <a href="{{ route('messages.read', $msg) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-semibold text-sm flex-shrink-0">
            {{ strtoupper(substr($msg->receiver->name ?? 'U', 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-700">To: {{ $msg->receiver->name ?? 'Unknown' }}</p>
                <span class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-sm text-gray-500 truncate">{{ $msg->subject ?: 'No subject' }}</p>
        </div>
    </a>
@empty
    <div class="p-12 text-center"><i class="fas fa-paper-plane text-4xl text-gray-300 mb-3"></i><p class="text-gray-500">No sent messages</p></div>
@endforelse
</div>
<div class="mt-6">{{ $messages->links() }}</div>
@endsection
