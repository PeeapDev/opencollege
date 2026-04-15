<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — OpenCollege</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' },
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .oc-sidebar { background: #0f172a; }
        .oc-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.55rem 1rem;
            font-size: 0.8125rem;
            color: #cbd5e1;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
            overflow: hidden;
        }
        .oc-nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .oc-nav-link.active { background: #2563eb; color: #fff; font-weight: 500; }
        .oc-nav-link i { width: 1.25rem; text-align: center; flex-shrink: 0; font-size: 0.875rem; }
        .oc-nav-section {
            padding: 1rem 1rem 0.25rem 1rem;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
        }
        .oc-nav-section.red-label { color: #f87171; }
        .oc-collapse-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.55rem 1rem;
            font-size: 0.8125rem;
            color: #cbd5e1;
            border-radius: 0.5rem;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            transition: all 0.15s ease;
        }
        .oc-collapse-btn:hover { background: rgba(255,255,255,0.08); color: #fff; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-50" x-data="{ sidebarOpen: true, mobileSidebar: false }">

<div class="flex h-full">
    {{-- Mobile Overlay --}}
    <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition.opacity></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col oc-sidebar transition-all duration-300 overflow-hidden"
           :class="[
               sidebarOpen ? 'w-64' : 'w-[4.5rem]',
               mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
           ]">
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 h-16 border-b border-white/10 flex-shrink-0">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
            </div>
            <div x-show="sidebarOpen" x-transition.opacity class="overflow-hidden min-w-0">
                <h1 class="text-white font-bold text-base leading-tight truncate">OpenCollege</h1>
                <p class="text-slate-400 text-[10px]">Management System</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 px-3" style="scrollbar-width: thin; scrollbar-color: #334155 transparent;">
            <a href="{{ route('dashboard') }}" class="oc-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Dashboard</span>
            </a>

            {{-- Academic --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>Academic</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('faculties.index') }}" class="oc-nav-link {{ request()->routeIs('faculties.*') ? 'active' : '' }}">
                <i class="fas fa-university"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Faculties</span>
            </a>
            <a href="{{ route('departments.index') }}" class="oc-nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Departments</span>
            </a>
            <a href="{{ route('programs.index') }}" class="oc-nav-link {{ request()->routeIs('programs.*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Programs</span>
            </a>
            <a href="{{ route('courses.index') }}" class="oc-nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                <i class="fas fa-chalkboard"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Courses</span>
            </a>

            {{-- People --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>People</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('students.index') }}" class="oc-nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Students</span>
            </a>
            <a href="{{ route('staff.index') }}" class="oc-nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Staff</span>
            </a>

            {{-- Admissions --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>Admissions</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('admissions.index') }}" class="oc-nav-link {{ request()->routeIs('admissions.*') ? 'active' : '' }}">
                <i class="fas fa-user-plus"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Applications</span>
            </a>

            {{-- Finance --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>Finance</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('invoices.index') }}" class="oc-nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Invoices</span>
            </a>
            <a href="{{ route('payments.index') }}" class="oc-nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Payments</span>
            </a>
            <a href="{{ route('peeappay.transactions') }}" class="oc-nav-link {{ request()->routeIs('peeappay.*') ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Online Payments</span>
            </a>

            {{-- Exam Board --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>Exam Board</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('exams.index') }}" class="oc-nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Exams</span>
            </a>
            <a href="{{ route('exam.schedules') }}" class="oc-nav-link {{ request()->routeIs('exam.schedules*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Exam Schedules</span>
            </a>
            <a href="{{ route('exam.grading') }}" class="oc-nav-link {{ request()->routeIs('exam.grading*') ? 'active' : '' }}">
                <i class="fas fa-pen-fancy"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Grade Entry</span>
            </a>
            <a href="{{ route('exam.results') }}" class="oc-nav-link {{ request()->routeIs('exam.results*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Result Publications</span>
            </a>

            {{-- Academics --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>Academics</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('attendance.index') }}" class="oc-nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Attendance</span>
            </a>
            <a href="{{ route('library.index') }}" class="oc-nav-link {{ request()->routeIs('library.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Library</span>
            </a>

            {{-- ID Cards & QR --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>ID Cards & QR</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('id_cards.index') }}" class="oc-nav-link {{ request()->routeIs('id_cards.*') ? 'active' : '' }}">
                <i class="fas fa-id-badge"></i>
                <span x-show="sidebarOpen" x-transition.opacity>ID Cards</span>
            </a>
            <a href="{{ route('qr.scanner') }}" class="oc-nav-link {{ request()->routeIs('qr.*') ? 'active' : '' }}">
                <i class="fas fa-qrcode"></i>
                <span x-show="sidebarOpen" x-transition.opacity>QR Scanner</span>
            </a>

            {{-- Communication --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>Communication</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('notices.index') }}" class="oc-nav-link {{ request()->routeIs('notices.*') ? 'active' : '' }}">
                <i class="fas fa-bullhorn"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Notices</span>
            </a>
            <a href="{{ route('messages.inbox') }}" class="oc-nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Messages</span>
            </a>

            {{-- Human Resources --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>Human Resources</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('hr.dashboard') }}" class="oc-nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i>
                <span x-show="sidebarOpen" x-transition.opacity>HR Dashboard</span>
            </a>
            <a href="{{ route('hr.leaves') }}" class="oc-nav-link {{ request()->routeIs('hr.leaves*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Leave Management</span>
            </a>
            <a href="{{ route('hr.payroll') }}" class="oc-nav-link {{ request()->routeIs('hr.payroll*') ? 'active' : '' }}">
                <i class="fas fa-money-check-alt"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Payroll</span>
            </a>
            <a href="{{ route('hr.directory') }}" class="oc-nav-link {{ request()->routeIs('hr.directory') ? 'active' : '' }}">
                <i class="fas fa-address-book"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Staff Directory</span>
            </a>

            {{-- Verification --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>Verification</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('nsi.index') }}" class="oc-nav-link {{ request()->routeIs('nsi.*') ? 'active' : '' }}">
                <i class="fas fa-id-card"></i>
                <span x-show="sidebarOpen" x-transition.opacity>NSI Verification</span>
            </a>

            {{-- Settings --}}
            <div class="oc-nav-section" x-show="sidebarOpen" x-transition.opacity>System</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-white/10"></div>
            <a href="{{ route('settings.index') }}" class="oc-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Settings</span>
            </a>

            {{-- Super Admin --}}
            @if(auth()->user()->hasRole('super_admin'))
            <div class="oc-nav-section red-label" x-show="sidebarOpen" x-transition.opacity>Super Admin</div>
            <div x-show="!sidebarOpen" class="mt-3 mb-1 mx-auto w-8 border-t border-red-500/30"></div>
            <a href="{{ route('superadmin.dashboard') }}" class="oc-nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-shield-alt"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Platform Dashboard</span>
            </a>
            <a href="{{ route('superadmin.colleges') }}" class="oc-nav-link {{ request()->routeIs('superadmin.colleges*') ? 'active' : '' }}">
                <i class="fas fa-school"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Manage Colleges</span>
            </a>
            @endif
        </nav>

        {{-- Collapse toggle --}}
        <div class="border-t border-white/10 p-3 flex-shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="oc-collapse-btn">
                <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'" style="width:1.25rem;text-align:center;"></i>
                <span x-show="sidebarOpen" x-transition.opacity>Collapse</span>
            </button>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 lg:ml-64" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-[4.5rem]'">
        {{-- Top bar --}}
        <header class="sticky top-0 z-30 bg-white border-b border-gray-200 h-16 flex items-center px-4 sm:px-6 flex-shrink-0">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-3">
                    <button @click="mobileSidebar = !mobileSidebar" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800">@yield('page_title', 'Dashboard')</h2>
                </div>
                <div class="flex items-center gap-3">
                    <button class="relative p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <span class="hidden sm:block text-sm text-gray-700 font-medium max-w-[120px] truncate">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <i class="fas fa-chevron-down text-[10px] text-gray-400 hidden sm:block"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                            </div>
                            <a href="#" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                                <i class="fas fa-user w-4 text-center text-gray-400"></i> Profile
                            </a>
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                                <i class="fas fa-cog w-4 text-center text-gray-400"></i> Settings
                            </a>
                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt w-4 text-center"></i> Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-4 sm:mx-6 mt-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-2 text-sm">
                <i class="fas fa-check-circle flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-4 sm:mx-6 mt-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-2 text-sm">
                <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-4 sm:p-6">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-100 px-6 py-3 text-center text-xs text-gray-400 flex-shrink-0">
            OpenCollege v1.0 &copy; {{ date('Y') }} <a href="https://github.com/PeeapDev" class="text-blue-600 hover:underline">PeeapDev</a> — Open Source College Management System
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
