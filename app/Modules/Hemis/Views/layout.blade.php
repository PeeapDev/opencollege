<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HEMIS') — {{ config('opencollege.country', 'National') }} Higher Education MIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800">

<div class="min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="w-64 bg-slate-900 text-slate-200 flex flex-col">
        <div class="px-6 py-5 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-blue-600 flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-white"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-white">HEMIS</div>
                    <div class="text-xs text-slate-400">{{ config('opencollege.country', 'National') }}</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            @php
                $nav = [
                    ['label'=>'Dashboard',         'icon'=>'fa-chart-line',     'route'=>'hemis.dashboard',            'match'=>'hemis*'],
                    ['label'=>'Institutions',      'icon'=>'fa-university',     'route'=>'hemis.institutions',         'match'=>'hemis/institutions*'],
                    ['label'=>'Students',          'icon'=>'fa-user-graduate',  'route'=>'hemis.students',             'match'=>'hemis/students*'],
                    ['label'=>'National Reports',  'icon'=>'fa-file-chart-line','route'=>'hemis.reports',              'match'=>'hemis/reports*'],
                ];
            @endphp
            @foreach($nav as $item)
                @php
                    $active = request()->is($item['match']);
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ $active ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas {{ $item['icon'] }} w-5 text-center"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

            <div class="pt-4 mt-4 border-t border-slate-800">
                <div class="px-3 text-xs uppercase tracking-wider text-slate-500 mb-2">Platform</div>
                <a href="/superadmin/colleges" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span>Manage Colleges</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-slate-800 text-xs text-slate-400">
            <div>HEMIS v0.1</div>
            <div class="mt-1">{{ config('opencollege.ministry', 'Ministry of Higher Education') }}</div>
        </div>
    </aside>

    {{-- Main --}}
    <main class="flex-1 flex flex-col">
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">@yield('page_title', 'Dashboard')</h1>
                @hasSection('subtitle')
                    <p class="text-sm text-slate-500 mt-0.5">@yield('subtitle')</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <div class="text-right text-sm">
                        <div class="font-medium text-slate-800">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
                    </div>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="text-sm text-slate-500 hover:text-red-600">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                @else
                    <a href="/login" class="text-sm text-blue-600 hover:underline">Sign in</a>
                @endauth
            </div>
        </header>

        <div class="flex-1 p-8 overflow-auto">
            @if (session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
