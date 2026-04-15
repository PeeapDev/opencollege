@extends('core::layouts.app')
@section('title', 'Library')
@section('page_title', 'Library')
@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-gray-900">Library</h3><p class="text-sm text-gray-500">Book catalog and issue management</p></div>
        <a href="{{ route('library.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-plus"></i> Add Book</a>
    </div>
    <div class="py-16 text-center"><i class="fas fa-book text-gray-300 text-4xl mb-4"></i><p class="text-gray-500">Library catalog is empty.</p>
        <a href="{{ route('library.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-plus"></i> Add First Book</a></div>
</div>
@endsection
