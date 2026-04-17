@extends('hemis::layout')
@section('title', 'National Reports')
@section('page_title', 'National Reports')
@section('subtitle', 'Aggregate statistics for tertiary education')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @php
        $reports = [
            ['title'=>'Enrollment', 'desc'=>'Students by institution, level, gender', 'route'=>'hemis.reports.enrollment', 'icon'=>'fa-users', 'color'=>'bg-blue-500', 'ready'=>true],
            ['title'=>'Gender Parity', 'desc'=>'Male/female distribution across programs', 'icon'=>'fa-venus-mars', 'color'=>'bg-pink-500', 'ready'=>false],
            ['title'=>'Completion Rates', 'desc'=>'Graduates vs enrolments per cohort', 'icon'=>'fa-graduation-cap', 'color'=>'bg-emerald-500', 'ready'=>false],
            ['title'=>'Program Distribution', 'desc'=>'Which disciplines are oversubscribed', 'icon'=>'fa-book-open', 'color'=>'bg-amber-500', 'ready'=>false],
            ['title'=>'Accreditation', 'desc'=>'Status by institution type', 'icon'=>'fa-certificate', 'color'=>'bg-indigo-500', 'ready'=>false],
            ['title'=>'Geographic Distribution', 'desc'=>'Tertiary institutions by district', 'icon'=>'fa-map-location-dot', 'color'=>'bg-rose-500', 'ready'=>false],
        ];
    @endphp
    @foreach($reports as $r)
        @if($r['ready'])
            <a href="{{ route($r['route']) }}" class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md hover:border-blue-300 transition block">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg {{ $r['color'] }} flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $r['icon'] }} text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900">{{ $r['title'] }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ $r['desc'] }}</p>
                    </div>
                </div>
            </a>
        @else
            <div class="bg-white rounded-xl border border-slate-200 p-5 opacity-60 cursor-not-allowed">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-slate-300 flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $r['icon'] }} text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-600">{{ $r['title'] }} <span class="text-xs font-normal text-amber-600 ml-1">Coming soon</span></h3>
                        <p class="text-xs text-slate-500 mt-1">{{ $r['desc'] }}</p>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

@endsection
