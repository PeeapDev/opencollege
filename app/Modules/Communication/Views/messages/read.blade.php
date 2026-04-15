@extends('core::layouts.app')
@section('title', 'Message')
@section('page_title', 'Message')

@section('content')
<div class="max-w-2xl">
    <div class="mb-4">
        <a href="{{ route('messages.inbox') }}" class="text-sm text-blue-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i> Back to Inbox</a>
    </div>
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-center gap-4 mb-6 pb-4 border-b">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-lg">
                {{ strtoupper(substr($message->sender->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">{{ $message->sender->name ?? 'Unknown' }}</p>
                <p class="text-xs text-gray-400">To: {{ $message->receiver->name ?? 'Unknown' }} • {{ $message->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
        @if($message->subject)
        <h3 class="font-semibold text-gray-900 mb-3">{{ $message->subject }}</h3>
        @endif
        <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $message->body }}</div>
    </div>
</div>
@endsection
