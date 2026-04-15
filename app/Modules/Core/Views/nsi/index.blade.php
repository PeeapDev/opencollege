@extends('core::layouts.app')
@section('title', 'NSI Verification')
@section('page_title', 'NSI Verification')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Verify Form --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="mb-5">
            <h3 class="text-lg font-semibold text-gray-900">Verify Student NSI</h3>
            <p class="text-sm text-gray-500 mt-0.5">Check a student's high school records from the SDSL system using their NSI number</p>
        </div>
        <form method="POST" action="{{ route('nsi.verify') }}" class="space-y-4" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NSI Number *</label>
                <input type="text" name="nsi_number" value="{{ old('nsi_number') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-mono tracking-wider" placeholder="e.g. NSI-2024-00001">
                @error('nsi_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" :disabled="loading" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-lg transition">
                <span x-show="!loading"><i class="fas fa-search mr-2"></i>Verify NSI</span>
                <span x-show="loading" x-cloak><i class="fas fa-spinner fa-spin mr-2"></i>Checking SDSL...</span>
            </button>
        </form>

        <div class="mt-5 p-4 bg-slate-50 rounded-lg border border-slate-200">
            <h4 class="text-sm font-semibold text-slate-700 mb-2"><i class="fas fa-info-circle text-blue-500 mr-1"></i> What is NSI?</h4>
            <p class="text-xs text-slate-500 leading-relaxed">The <strong>National Student Identifier (NSI)</strong> is a unique number assigned to every student in Sierra Leone's education system. It bridges high school (SDSL) and college records, enabling verified academic continuity when a student transitions from secondary to tertiary education.</p>
        </div>
    </div>

    {{-- Verification History --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Verification History</h3>
        </div>
        @if($verifications->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr><th class="px-5 py-2.5 text-left">NSI</th><th class="px-5 py-2.5 text-left">Student</th><th class="px-5 py-2.5 text-left">High School</th><th class="px-5 py-2.5 text-left">Year</th><th class="px-5 py-2.5 text-left">Status</th><th class="px-5 py-2.5 text-left">Linked To</th><th class="px-5 py-2.5 text-left">Date</th><th class="px-5 py-2.5 text-left"></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($verifications as $v)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-blue-600 text-xs">{{ $v->nsi_number }}</td>
                        <td class="px-5 py-3 text-gray-900">{{ $v->student_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $v->high_school_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $v->graduation_year ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $v->verification_status === 'verified' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $v->verification_status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $v->verification_status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $v->verification_status === 'not_found' ? 'bg-gray-100 text-gray-600' : '' }}
                            ">{{ ucfirst(str_replace('_', ' ', $v->verification_status)) }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $v->linked_student_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ \Carbon\Carbon::parse($v->created_at)->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3">
                            @if($v->verification_status === 'verified')
                            <a href="{{ route('nsi.show', $v->id) }}" class="text-xs text-blue-600 hover:underline"><i class="fas fa-history mr-0.5"></i>Full History</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $verifications->links() }}</div>
        @else
        <div class="py-16 text-center">
            <i class="fas fa-id-card text-gray-200 text-4xl mb-3"></i>
            <p class="text-gray-400 text-sm">No verifications yet. Enter an NSI number to begin.</p>
        </div>
        @endif
    </div>
</div>
@endsection
