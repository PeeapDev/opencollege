@extends('hemis::layout')
@section('title', 'Enrollment Report')
@section('page_title', 'Enrollment Report')
@section('subtitle', 'Students aggregated across all higher-education institutions')

@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('hemis.reports') }}" class="text-sm text-slate-500 hover:text-slate-800">
        <i class="fas fa-arrow-left mr-1"></i> All reports
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- By institution --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 lg:col-span-2">
        <h3 class="text-base font-semibold text-slate-900 mb-4">By Institution</h3>
        @if ($byInstitution->isEmpty())
            <p class="text-sm text-slate-500">No enrollment data yet.</p>
        @else
            <div class="space-y-2">
                @php $max = $byInstitution->max('total') ?: 1; @endphp
                @foreach ($byInstitution->sortByDesc('total') as $row)
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-700">{{ $row['institution'] }}
                                <span class="text-xs text-slate-400">({{ $row['code'] }})</span>
                            </span>
                            <span class="font-medium">{{ number_format($row['total']) }}</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden mt-1">
                            <div class="h-full bg-blue-500" style="width:{{ ($row['total'] / $max) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- By level --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-base font-semibold text-slate-900 mb-4">By Level</h3>
        @if (empty($byLevel))
            <p class="text-sm text-slate-500">No data.</p>
        @else
            <div class="space-y-3">
                @php $total = array_sum($byLevel); @endphp
                @foreach ($byLevel as $level => $count)
                    @php $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-sm"><span class="capitalize">{{ $level }}</span><span>{{ $count }}</span></div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden mt-1">
                            <div class="h-full bg-amber-500" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- By gender --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 lg:col-span-3">
        <h3 class="text-base font-semibold text-slate-900 mb-4">Gender Parity</h3>
        @if (empty($byGender))
            <p class="text-sm text-slate-500">No data.</p>
        @else
            <div class="flex items-center gap-6">
                @php $total = array_sum($byGender); @endphp
                @foreach ($byGender as $gender => $count)
                    @php
                        $pct = $total > 0 ? round($count / $total * 100) : 0;
                        $color = $gender === 'female' ? 'text-pink-600' : ($gender === 'male' ? 'text-blue-600' : 'text-slate-600');
                    @endphp
                    <div class="flex-1 text-center">
                        <div class="text-4xl font-bold {{ $color }}">{{ $pct }}%</div>
                        <div class="text-sm text-slate-600 capitalize mt-1">{{ $gender }}</div>
                        <div class="text-xs text-slate-400">{{ number_format($count) }} students</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
