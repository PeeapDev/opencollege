{{-- Reusable CRUD index layout --}}
@extends('core::layouts.app')

@section('title', $pageTitle ?? 'List')
@section('page_title', $pageTitle ?? 'List')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ $pageTitle ?? 'Records' }}</h3>
            @isset($pageSubtitle)
                <p class="text-sm text-gray-500 mt-0.5">{{ $pageSubtitle }}</p>
            @endisset
        </div>
        @isset($createRoute)
            <a href="{{ $createRoute }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="fas fa-plus"></i>
                {{ $createLabel ?? 'Add New' }}
            </a>
        @endisset
    </div>

    @if(isset($items) && $items->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                    <tr>
                        @foreach($columns as $col)
                            <th class="px-5 py-3 text-left font-medium">{{ $col }}</th>
                        @endforeach
                        <th class="px-5 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @yield('table_rows')
                </tbody>
            </table>
        </div>
        @if(method_exists($items, 'links'))
            <div class="p-4 border-t border-gray-100">
                {{ $items->links() }}
            </div>
        @endif
    @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas {{ $emptyIcon ?? 'fa-folder-open' }} text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 font-medium">{{ $emptyMessage ?? 'No records found' }}</p>
            @isset($createRoute)
                <a href="{{ $createRoute }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    <i class="fas fa-plus"></i> {{ $createLabel ?? 'Add First Record' }}
                </a>
            @endisset
        </div>
    @endif
</div>
@endsection
