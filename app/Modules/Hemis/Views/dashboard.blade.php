@extends('hemis::layout')
@section('title', 'Dashboard')
@section('page_title', 'National Tertiary Education Dashboard')
@section('subtitle', 'Aggregate view across all registered higher-education institutions')

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    @php
        $cards = [
            ['label'=>'Institutions',      'value'=>$stats['institutions'],        'icon'=>'fa-university',        'color'=>'bg-blue-500'],
            ['label'=>'Active',             'value'=>$stats['institutions_active'], 'icon'=>'fa-check-circle',      'color'=>'bg-emerald-500'],
            ['label'=>'Students',           'value'=>$stats['students'],            'icon'=>'fa-user-graduate',     'color'=>'bg-indigo-500'],
            ['label'=>'Programs',           'value'=>$stats['programs'],            'icon'=>'fa-book-open',         'color'=>'bg-amber-500'],
            ['label'=>'Total Users',        'value'=>$stats['users'],               'icon'=>'fa-users',             'color'=>'bg-rose-500'],
        ];
    @endphp
    @foreach($cards as $c)
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium text-slate-500 uppercase">{{ $c['label'] }}</div>
                    <div class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($c['value']) }}</div>
                </div>
                <div class="w-12 h-12 rounded-lg {{ $c['color'] }} flex items-center justify-center">
                    <i class="fas {{ $c['icon'] }} text-white text-lg"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Second row — breakdowns --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Institution types --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-base font-semibold text-slate-900 mb-4">Institution Types</h3>
        @if (empty($byType))
            <p class="text-sm text-slate-500">No institutions registered yet.</p>
        @else
            <div class="space-y-3">
                @foreach ($byType as $type => $count)
                    @php
                        $total = array_sum($byType);
                        $pct = $total > 0 ? round($count / $total * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-700 capitalize">{{ $type ?: 'Unspecified' }}</span>
                            <span class="text-slate-500 font-medium">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Accreditation --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-base font-semibold text-slate-900 mb-4">Accreditation Status</h3>
        @if (empty($byAccreditation))
            <p class="text-sm text-slate-500">No data.</p>
        @else
            <div class="space-y-3">
                @foreach ($byAccreditation as $status => $count)
                    @php
                        $total = array_sum($byAccreditation);
                        $pct = $total > 0 ? round($count / $total * 100) : 0;
                        $color = match($status) {
                            'accredited'   => 'bg-emerald-500',
                            'pending'      => 'bg-amber-500',
                            'provisional'  => 'bg-blue-500',
                            'suspended'    => 'bg-red-500',
                            default        => 'bg-slate-500',
                        };
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-700 capitalize">{{ str_replace('_', ' ', $status ?: 'Unspecified') }}</span>
                            <span class="text-slate-500 font-medium">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $color }}" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Gender split --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-base font-semibold text-slate-900 mb-4">Student Gender Parity</h3>
        @if (empty($byGender))
            <p class="text-sm text-slate-500">No students registered yet.</p>
        @else
            <div class="space-y-3">
                @foreach ($byGender as $gender => $count)
                    @php
                        $total = array_sum($byGender);
                        $pct = $total > 0 ? round($count / $total * 100) : 0;
                        $color = $gender === 'female' ? 'bg-pink-500' : ($gender === 'male' ? 'bg-blue-500' : 'bg-slate-500');
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-700 capitalize">{{ $gender ?: 'Unspecified' }}</span>
                            <span class="text-slate-500 font-medium">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $color }}" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Recent institutions --}}
<div class="bg-white rounded-xl border border-slate-200">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h3 class="text-base font-semibold text-slate-900">Recently Registered Institutions</h3>
        <a href="{{ route('hemis.institutions') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
    </div>
    @if ($recentInstitutions->isEmpty())
        <div class="p-8 text-center text-sm text-slate-500">
            No institutions registered yet.
            <a href="/superadmin/colleges/create" class="text-blue-600 hover:underline ml-1">Register one</a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                    <tr>
                        <th class="text-left px-6 py-3">Name</th>
                        <th class="text-left px-6 py-3">Code</th>
                        <th class="text-left px-6 py-3">Type</th>
                        <th class="text-left px-6 py-3">Accreditation</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Portal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($recentInstitutions as $inst)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3">
                                <a href="{{ route('hemis.institutions.show', $inst->id) }}" class="font-medium text-slate-900 hover:text-blue-600">{{ $inst->name }}</a>
                            </td>
                            <td class="px-6 py-3 text-slate-600">{{ $inst->code }}</td>
                            <td class="px-6 py-3 text-slate-600 capitalize">{{ $inst->type }}</td>
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
                            <td class="px-6 py-3">
                                <a href="https://{{ $inst->domain }}.college.edu.sl" target="_blank" class="text-blue-600 hover:underline text-xs">
                                    {{ $inst->domain }}.college.edu.sl ↗
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
