@extends('core::layouts.app')

@section('title', 'Platform Dashboard')
@section('page_title')
    <div>
        <span>Platform Dashboard</span>
        <p class="text-xs font-normal text-gray-400 mt-0.5">OpenCollege Super Admin</p>
    </div>
@endsection

@section('content')
{{-- Platform Banner --}}
<div class="relative overflow-hidden bg-gradient-to-r from-slate-800 via-slate-900 to-gray-900 rounded-2xl p-6 mb-6 text-white">
    <div class="relative z-10">
        <h2 class="text-xl font-bold">OpenCollege Platform</h2>
        <p class="text-slate-400 text-sm mt-1">Multi-Tenant College Management &middot; {{ now()->format('l, d F Y') }}</p>
        <div class="flex gap-3 mt-4">
            <a href="{{ route('superadmin.colleges.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium transition"><i class="fas fa-plus mr-1.5"></i>Register College</a>
            <a href="{{ route('superadmin.colleges') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition"><i class="fas fa-school mr-1.5"></i>All Colleges</a>
        </div>
    </div>
    <div class="absolute right-6 top-6 opacity-10 text-8xl"><i class="fas fa-shield-alt"></i></div>
</div>

{{-- Platform KPIs --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center"><i class="fas fa-school text-blue-600"></i></div>
            <div><p class="text-2xl font-bold text-gray-900 leading-none">{{ $stats['total_colleges'] }}</p><p class="text-[11px] text-gray-500 mt-0.5">Colleges</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div>
            <div><p class="text-2xl font-bold text-gray-900 leading-none">{{ $stats['active_colleges'] }}</p><p class="text-[11px] text-gray-500 mt-0.5">Active</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center"><i class="fas fa-clock text-amber-600"></i></div>
            <div><p class="text-2xl font-bold text-gray-900 leading-none">{{ $stats['pending_colleges'] }}</p><p class="text-[11px] text-gray-500 mt-0.5">Pending</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center"><i class="fas fa-user-graduate text-purple-600"></i></div>
            <div><p class="text-2xl font-bold text-gray-900 leading-none">{{ number_format($stats['total_students']) }}</p><p class="text-[11px] text-gray-500 mt-0.5">Students</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center"><i class="fas fa-chalkboard-teacher text-rose-600"></i></div>
            <div><p class="text-2xl font-bold text-gray-900 leading-none">{{ number_format($stats['total_staff']) }}</p><p class="text-[11px] text-gray-500 mt-0.5">Staff</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center"><i class="fas fa-users text-teal-600"></i></div>
            <div><p class="text-2xl font-bold text-gray-900 leading-none">{{ number_format($stats['total_users']) }}</p><p class="text-[11px] text-gray-500 mt-0.5">Total Users</p></div>
        </div>
    </div>
</div>

{{-- Chart + College List --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-1">College Registrations</h3>
        <div id="chart-registrations" class="h-64"></div>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Registered Colleges</h3>
            <a href="{{ route('superadmin.colleges') }}" class="text-xs text-blue-600 hover:underline font-medium">View All <i class="fas fa-arrow-right ml-0.5"></i></a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr><th class="px-5 py-2.5 text-left">College</th><th class="px-5 py-2.5 text-left">Type</th><th class="px-5 py-2.5 text-left">Students</th><th class="px-5 py-2.5 text-left">Staff</th><th class="px-5 py-2.5 text-left">Status</th><th class="px-5 py-2.5 text-right">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($colleges as $col)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">{{ strtoupper(substr($col->code, 0, 2)) }}</div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $col->name }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $col->domain }}.college.edu.sl</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs capitalize">{{ $col->type ?? 'college' }}</span></td>
                        <td class="px-5 py-3 font-medium">{{ $col->students_count ?? 0 }}</td>
                        <td class="px-5 py-3 font-medium">{{ $col->staff_members_count ?? 0 }}</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $col->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $col->active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('superadmin.colleges.toggle', $col) }}" class="inline">
                                @csrf
                                <button class="text-xs px-2 py-1 rounded {{ $col->active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">{{ $col->active ? 'Disable' : 'Enable' }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400"><i class="fas fa-school text-3xl mb-2"></i><p>No colleges registered yet</p></td></tr>
                    @endforelse
                </tbody>
            </table>
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
    const regData = @json($monthlyData);
    new ApexCharts(document.querySelector("#chart-registrations"), {
        chart: { type: 'bar', height: 256, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [{ name: 'Registrations', data: Object.values(regData) }],
        xaxis: { categories: Object.keys(regData).map(m => m.substr(5)), labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
        colors: ['#3b82f6'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
    }).render();
});
</script>
@endpush
