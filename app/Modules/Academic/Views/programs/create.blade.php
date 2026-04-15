@extends('core::layouts.app')
@section('title', 'Add Program')
@section('page_title', 'Add Program')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('programs.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. BSc Computer Science">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. BSC-CS">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                    <select name="level" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="certificate" {{ old('level') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                        <option value="diploma" {{ old('level') == 'diploma' ? 'selected' : '' }}>Diploma</option>
                        <option value="higher_diploma" {{ old('level') == 'higher_diploma' ? 'selected' : '' }}>Higher Diploma</option>
                        <option value="bachelors" {{ old('level', 'bachelors') == 'bachelors' ? 'selected' : '' }}>Bachelor's</option>
                        <option value="masters" {{ old('level') == 'masters' ? 'selected' : '' }}>Master's</option>
                        <option value="doctorate" {{ old('level') == 'doctorate' ? 'selected' : '' }}>Doctorate</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                    <select name="department_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->faculty->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (years) *</label>
                    <input type="number" name="duration_years" value="{{ old('duration_years', 4) }}" min="1" max="10" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Credits</label>
                <input type="number" name="total_credits" value="{{ old('total_credits') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"><i class="fas fa-save mr-2"></i>Save</button>
                <a href="{{ route('programs.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
