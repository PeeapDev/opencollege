@extends('core::layouts.app')
@section('title', 'Edit Notice')
@section('page_title', 'Edit Notice')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border p-6">
        <form method="POST" action="{{ route('notices.update', $notice) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $notice->title) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea name="content" rows="6" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('content', $notice->content) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Audience</label>
                    <select name="audience" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                        @foreach(['all','students','staff'] as $a)<option value="{{ $a }}" {{ $notice->audience === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Publish Date</label>
                    <input type="date" name="publish_date" value="{{ old('publish_date', $notice->publish_date->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date', $notice->expiry_date?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_pinned" value="1" {{ $notice->is_pinned ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-blue-600">
                        Pin this notice
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">Update Notice</button>
                <a href="{{ route('notices.index') }}" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
