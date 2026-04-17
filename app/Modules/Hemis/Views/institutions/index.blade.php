@extends('hemis::layout')
@section('title', 'Institutions')
@section('page_title', 'Institution Directory')
@section('subtitle', 'All higher-education institutions registered with HEMIS')

@section('content')

<div class="bg-white rounded-xl border border-slate-200 mb-4 p-4">
    <form method="GET" action="{{ route('hemis.institutions') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search name, code, domain, city"
               class="md:col-span-2 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="type" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
            <option value="">All types</option>
            @foreach(['university','college','polytechnic'] as $t)
                <option value="{{ $t }}" @selected(($type ?? '') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
        <select name="accreditation" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
            <option value="">Any accreditation</option>
            @foreach(['accredited','pending','provisional','suspended'] as $a)
                <option value="{{ $a }}" @selected(($accreditation ?? '') === $a)>{{ ucfirst($a) }}</option>
            @endforeach
        </select>
        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if($search || $type || $accreditation)
                <a href="{{ route('hemis.institutions') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm hover:bg-slate-50">Clear</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl border border-slate-200">
    @if ($institutions->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-university text-5xl text-slate-300 mb-3"></i>
            <p class="text-slate-500">No institutions match your filters.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                    <tr>
                        <th class="text-left px-6 py-3">Institution</th>
                        <th class="text-left px-6 py-3">Type</th>
                        <th class="text-left px-6 py-3">Location</th>
                        <th class="text-left px-6 py-3">Accreditation</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Registered</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($institutions as $inst)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3">
                                <a href="{{ route('hemis.institutions.show', $inst->id) }}" class="font-medium text-slate-900 hover:text-blue-600">{{ $inst->name }}</a>
                                <div class="text-xs text-slate-500">{{ $inst->code }} &middot; {{ $inst->domain }}.college.edu.sl</div>
                            </td>
                            <td class="px-6 py-3 text-slate-600 capitalize">{{ $inst->type ?: '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $inst->city ?: '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-block px-2 py-0.5 text-xs rounded-full capitalize
                                    {{ $inst->accreditation_status === 'accredited' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $inst->accreditation_status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $inst->accreditation_status === 'suspended' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ !in_array($inst->accreditation_status, ['accredited','pending','suspended']) ? 'bg-slate-100 text-slate-700' : '' }}">
                                    {{ str_replace('_', ' ', $inst->accreditation_status ?: 'unknown') }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                @if ($inst->active)
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700">Active</span>
                                @else
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-slate-100 text-slate-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-slate-500 text-xs">{{ $inst->created_at?->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-slate-200">
            {{ $institutions->links() }}
        </div>
    @endif
</div>

@endsection
