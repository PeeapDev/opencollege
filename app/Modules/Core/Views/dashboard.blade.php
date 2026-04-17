 
@extends('core::layouts.app')

@section('title', 'Dashboard')
@section('page_title')
    <div>
        <span>Dashboard</span>
        <p class="text-xs font-normal text-gray-400 mt-0.5">{{ $institution->name ?? 'OpenCollege' }}</p>
    </div>
@endsection

@section('content')
{{-- Welcome Banner --}}
<div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-6 mb-6 text-white">
    <div class="relative z-10">
        <h2 class="text-xl font-bold">Welcome back, {{ auth()->user()->name }}</h2>
        <p class="text-blue-200 text-sm mt-1">{{ $institution->name ?? 'Your College' }} &middot; {{ now()->format('l, d F Y') }}</p>
        <div class="flex gap-3 mt-4">
            <a href="{{ route('students.create') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg text-sm font-medium transition"><i class="fas fa-user-plus mr-1.5"></i>Admit Student</a>
            <a href="{{ route('nsi.index') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg text-sm font-medium transition"><i class="fas fa-id-card mr-1.5"></i>Verify NSI</a>
        </div>
    </div>
    <div class="absolute right-0 top-0 w-64 h-full opacity-10">
        <svg viewBox="0 0 200 200" class="w-full h-full"><circle cx="100" cy="100" r="80" fill="white"/><circle cx="150" cy="50" r="50" fill="white"/></svg>
    </div>
</div>

{{-- Stat cards — SDSL-style gradients with hover lift --}}
<style>
    .oc-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; margin-bottom: 24px; }
    @media (max-width: 1024px) { .oc-stats { grid-template-columns: repeat(2, minmax(0,1fr)); } }
    @media (max-width: 560px)  { .oc-stats { grid-template-columns: 1fr; } }
    .oc-card {
        position: relative; overflow: hidden;
        border-radius: 14px; padding: 22px 24px; color: #fff;
        transition: transform .25s ease, box-shadow .25s ease;
        display: block;
    }
    .oc-card:hover { transform: translateY(-4px); box-shadow: 0 14px 32px rgba(0,0,0,0.22); }
    .oc-card .oc-icon {
        position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
        font-size: 64px; opacity: .14;
        transition: opacity .25s ease, transform .25s ease;
    }
    .oc-card:hover .oc-icon { opacity: .28; transform: translateY(-50%) scale(1.08); }
    .oc-card .oc-label { font-size: 12px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; opacity: .9; margin-bottom: 8px; }
    .oc-card .oc-num   { font-size: 38px; font-weight: 800; line-height: 1; margin-bottom: 4px; }
    .oc-card .oc-sub   { font-size: 11px; opacity: .78; }
    .oc-card-trend {
        position: absolute; bottom: 14px; right: 18px;
        font-size: 11px; font-weight: 700; opacity: .85;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .oc-c1 { background: linear-gradient(135deg, #0891b2, #06b6d4); }
    .oc-c2 { background: linear-gradient(135deg, #7c3aed, #a78bfa); }
    .oc-c3 { background: linear-gradient(135deg, #059669, #34d399); }
    .oc-c4 { background: linear-gradient(135deg, #d97706, #fbbf24); }
    .oc-c5 { background: linear-gradient(135deg, #0d9488, #2dd4bf); }
    .oc-c6 { background: linear-gradient(135deg, #e11d48, #fb7185); }
    .oc-c7 { background: linear-gradient(135deg, #2563eb, #60a5fa); }
    .oc-c8 { background: linear-gradient(135deg, #b45309, #f59e0b); }
    .oc-c9 { background: linear-gradient(135deg, #4f46e5, #818cf8); }
    .oc-c10{ background: linear-gradient(135deg, #be185d, #ec4899); }
    .oc-card a { color: inherit; }
</style>

@php
    $currency = app('institution')->currency_symbol ?? config('opencollege.default_currency', '');
    $paymentRate = ($stats['paid_invoices'] + $stats['pending_invoices']) > 0
        ? round($stats['paid_invoices'] / ($stats['paid_invoices'] + $stats['pending_invoices']) * 100)
        : 0;
    $genderParity = $stats['total_students'] > 0
        ? round(($stats['female_students'] / $stats['total_students']) * 100)
        : 0;
@endphp

{{-- Row 1 — people & academic structure --}}
<div class="oc-stats">
    <a href="{{ route('students.index') }}" class="oc-card oc-c1">
        <i class="fa fa-user-graduate oc-icon"></i>
        <div class="oc-label">Students</div>
        <div class="oc-num" data-counter="{{ $stats['total_students'] }}">{{ number_format($stats['total_students']) }}</div>
        <div class="oc-sub">Active enrolled students</div>
    </a>
    <a href="{{ route('staff.index') }}" class="oc-card oc-c2">
        <i class="fa fa-chalkboard-teacher oc-icon"></i>
        <div class="oc-label">Staff</div>
        <div class="oc-num" data-counter="{{ $stats['total_staff'] }}">{{ number_format($stats['total_staff']) }}</div>
        <div class="oc-sub">Teaching &amp; admin staff</div>
    </a>
    <a href="{{ route('programs.index') }}" class="oc-card oc-c3">
        <i class="fa fa-book-open oc-icon"></i>
        <div class="oc-label">Programs</div>
        <div class="oc-num" data-counter="{{ $stats['total_programs'] }}">{{ number_format($stats['total_programs']) }}</div>
        <div class="oc-sub">Across {{ $stats['total_departments'] }} departments</div>
    </a>
    <a href="{{ route('courses.index') }}" class="oc-card oc-c4">
        <i class="fa fa-chalkboard oc-icon"></i>
        <div class="oc-label">Courses</div>
        <div class="oc-num" data-counter="{{ $stats['total_courses'] }}">{{ number_format($stats['total_courses']) }}</div>
        <div class="oc-sub">Offered this academic year</div>
    </a>
</div>

{{-- Row 2 — finance & health --}}
<div class="oc-stats">
    <div class="oc-card oc-c5">
        <i class="fa fa-sack-dollar oc-icon"></i>
        <div class="oc-label">Total Revenue</div>
        <div class="oc-num">{{ $currency }}{{ number_format($stats['total_revenue'], 0) }}</div>
        <div class="oc-sub">Collected to date</div>
    </div>
    <a href="{{ route('invoices.index') }}" class="oc-card oc-c6">
        <i class="fa fa-file-invoice-dollar oc-icon"></i>
        <div class="oc-label">Outstanding Invoices</div>
        <div class="oc-num" data-counter="{{ $stats['pending_invoices'] }}">{{ number_format($stats['pending_invoices']) }}</div>
        <div class="oc-sub">Unpaid, partial, or overdue</div>
        @if($stats['pending_invoices'] > 0)
            <span class="oc-card-trend"><i class="fa fa-circle-exclamation"></i> action needed</span>
        @endif
    </a>
    <div class="oc-card oc-c7">
        <i class="fa fa-circle-check oc-icon"></i>
        <div class="oc-label">Payment Collection</div>
        <div class="oc-num">{{ $paymentRate }}%</div>
        <div class="oc-sub">{{ $stats['paid_invoices'] }} of {{ $stats['paid_invoices'] + $stats['pending_invoices'] }} invoices paid</div>
    </div>
    <div class="oc-card oc-c8">
        <i class="fa fa-venus-mars oc-icon"></i>
        <div class="oc-label">Gender Parity</div>
        <div class="oc-num">{{ $genderParity }}% <span style="font-size:16px; opacity:.8;">♀</span></div>
        <div class="oc-sub">{{ $stats['female_students'] }} female &middot; {{ $stats['male_students'] }} male</div>
    </div>
</div>

<script>
// Count-up animation for the stat numbers
(function(){
    var els = document.querySelectorAll('.oc-num[data-counter]');
    els.forEach(function(el){
        var target = parseInt(el.dataset.counter) || 0;
        if (target < 10) return; // not worth animating
        var duration = 900, start = performance.now();
        function step(now){
            var t = Math.min(1, (now - start) / duration);
            var eased = 1 - Math.pow(1 - t, 3);
            el.textContent = Math.floor(eased * target).toLocaleString();
            if (t < 1) requestAnimationFrame(step);
        }
        el.textContent = '0';
        requestAnimationFrame(step);
    });
})();
</script>

{{-- Charts Row 1: Enrollment Trend + Students by Program --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-sm font-semibold text-gray-900">Enrollment Trend</h3>
            <span class="text-xs text-gray-400">Last 12 months</span>
        </div>
        <div id="chart-enrollment" class="h-64"></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Students by Program</h3>
        <div id="chart-programs" class="h-64"></div>
    </div>
</div>

{{-- Charts Row 2: Revenue + Gender + Finance KPI --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-sm font-semibold text-gray-900">Revenue</h3>
            <span class="text-xs text-gray-400">Last 6 months</span>
        </div>
        <div id="chart-revenue" class="h-56"></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Gender Distribution</h3>
        <div id="chart-gender" class="h-48"></div>
        <div class="flex justify-center gap-6 mt-2 text-xs">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Male: {{ $stats['male_students'] }}</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span> Female: {{ $stats['female_students'] }}</span>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
        <h3 class="text-sm font-semibold text-gray-900">Finance Overview</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center gap-2"><i class="fas fa-file-invoice-dollar text-blue-500"></i><span class="text-sm text-gray-700">Total Invoiced</span></div>
                <span class="font-bold text-gray-900">Le {{ number_format($stats['total_invoiced']) }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-gray-700">Collected</span></div>
                <span class="font-bold text-green-700">Le {{ number_format($stats['total_revenue']) }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                <div class="flex items-center gap-2"><i class="fas fa-exclamation-triangle text-red-500"></i><span class="text-sm text-gray-700">Outstanding</span></div>
                <span class="font-bold text-red-700">Le {{ number_format($stats['total_invoiced'] - $stats['total_revenue']) }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg">
                <div class="flex items-center gap-2"><i class="fas fa-clock text-amber-500"></i><span class="text-sm text-gray-700">Pending Invoices</span></div>
                <span class="font-bold text-amber-700">{{ $stats['pending_invoices'] }}</span>
            </div>
            @if($stats['total_invoiced'] > 0)
            <div class="pt-1">
                <div class="flex justify-between text-xs text-gray-500 mb-1"><span>Collection Rate</span><span>{{ round($stats['total_revenue'] / max($stats['total_invoiced'], 1) * 100) }}%</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-green-500 h-2 rounded-full" style="width: {{ min(round($stats['total_revenue'] / max($stats['total_invoiced'], 1) * 100), 100) }}%"></div></div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Staff by Department + Student Status --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Staff by Department</h3>
        <div id="chart-staff-dept" class="h-56"></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Student Status</h3>
        <div id="chart-student-status" class="h-56"></div>
    </div>
</div>

{{-- Recent Activity Tables --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Students --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Admissions</h3>
            <a href="{{ route('students.index') }}" class="text-xs text-blue-600 hover:underline font-medium">View All <i class="fas fa-arrow-right ml-0.5"></i></a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentStudents as $student)
            <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0 {{ $student->gender === 'female' ? 'bg-pink-100 text-pink-600' : 'bg-blue-100 text-blue-600' }}">{{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $student->user->name ?? 'Unknown' }}</p>
                    <p class="text-[11px] text-gray-400">{{ $student->student_id }} &middot; {{ $student->program->name ?? '' }} &middot; Year {{ $student->current_year }}</p>
                </div>
                <span class="px-2 py-0.5 text-[10px] rounded-full font-medium {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($student->status) }}</span>
            </div>
            @empty
            <div class="px-5 py-10 text-center"><i class="fas fa-user-graduate text-gray-200 text-3xl"></i><p class="text-gray-400 text-sm mt-2">No students admitted yet</p></div>
            @endforelse
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Payments</h3>
            <a href="{{ route('payments.index') }}" class="text-xs text-blue-600 hover:underline font-medium">View All <i class="fas fa-arrow-right ml-0.5"></i></a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentPayments as $payment)
            <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition">
                <div class="w-9 h-9 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 flex-shrink-0"><i class="fas fa-receipt text-sm"></i></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $payment->student->user->name ?? 'Unknown' }}</p>
                    <p class="text-[11px] text-gray-400">{{ $payment->receipt_number }} &middot; {{ ucfirst(str_replace('_',' ',$payment->method)) }} &middot; {{ $payment->payment_date->format('d M Y') }}</p>
                </div>
                <span class="text-sm font-bold text-emerald-700">Le {{ number_format($payment->amount, 0) }}</span>
            </div>
            @empty
            <div class="px-5 py-10 text-center"><i class="fas fa-receipt text-gray-200 text-3xl"></i><p class="text-gray-400 text-sm mt-2">No payments recorded yet</p></div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = { blue: '#3b82f6', purple: '#8b5cf6', emerald: '#10b981', amber: '#f59e0b', pink: '#ec4899', red: '#ef4444', teal: '#14b8a6', rose: '#f43f5e', indigo: '#6366f1', cyan: '#06b6d4' };
    const palette = [colors.blue, colors.emerald, colors.purple, colors.amber, colors.pink, colors.teal, colors.rose, colors.indigo, colors.cyan, colors.red];

    // Enrollment Trend
    const enrollData = @json($enrollmentTrend);
    new ApexCharts(document.querySelector("#chart-enrollment"), {
        chart: { type: 'area', height: 256, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [{ name: 'New Students', data: Object.values(enrollData) }],
        xaxis: { categories: Object.keys(enrollData), labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
        yaxis: { labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
        colors: [colors.blue],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
        stroke: { curve: 'smooth', width: 2.5 },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        tooltip: { theme: 'light' },
    }).render();

    // Students by Program
    const progData = @json($studentsByProgram);
    new ApexCharts(document.querySelector("#chart-programs"), {
        chart: { type: 'donut', height: 256, fontFamily: 'inherit' },
        series: Object.values(progData),
        labels: Object.keys(progData).map(l => l.length > 20 ? l.substr(0,18)+'...' : l),
        colors: palette,
        legend: { position: 'bottom', fontSize: '11px', labels: { colors: '#6b7280' } },
        dataLabels: { enabled: false },
        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700 } } } } },
    }).render();

    // Revenue
    const revData = @json($revenueTrend);
    new ApexCharts(document.querySelector("#chart-revenue"), {
        chart: { type: 'bar', height: 224, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [{ name: 'Revenue', data: Object.values(revData) }],
        xaxis: { categories: Object.keys(revData), labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
        yaxis: { labels: { formatter: v => 'Le ' + (v/1000).toFixed(0) + 'K', style: { fontSize: '11px', colors: '#9ca3af' } } },
        colors: [colors.emerald],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
    }).render();

    // Gender
    new ApexCharts(document.querySelector("#chart-gender"), {
        chart: { type: 'donut', height: 192, fontFamily: 'inherit' },
        series: [{{ $stats['male_students'] }}, {{ $stats['female_students'] }}],
        labels: ['Male', 'Female'],
        colors: [colors.blue, colors.pink],
        legend: { show: false },
        dataLabels: { enabled: false },
        plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '12px', fontWeight: 700 } } } } },
    }).render();

    // Staff by Department
    const staffData = @json($staffByDept);
    new ApexCharts(document.querySelector("#chart-staff-dept"), {
        chart: { type: 'bar', height: 224, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [{ name: 'Staff', data: Object.values(staffData) }],
        xaxis: { categories: Object.keys(staffData).map(l => l.length > 15 ? l.substr(0,13)+'...' : l), labels: { style: { fontSize: '10px', colors: '#9ca3af' }, rotate: -45 } },
        colors: [colors.purple],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
    }).render();

    // Student Status
    const statusData = @json($studentsByStatus);
    const statusColors = { active: colors.emerald, graduated: colors.blue, suspended: colors.red, withdrawn: '#6b7280', deferred: colors.amber };
    new ApexCharts(document.querySelector("#chart-student-status"), {
        chart: { type: 'bar', height: 224, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [{ name: 'Students', data: Object.values(statusData) }],
        xaxis: { categories: Object.keys(statusData).map(s => s.charAt(0).toUpperCase()+s.slice(1)), labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
        colors: [colors.blue],
        fill: { colors: Object.keys(statusData).map(s => statusColors[s] || '#6b7280') },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '45%', distributed: true } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        legend: { show: false },
    }).render();
});
</script>
@endpush

