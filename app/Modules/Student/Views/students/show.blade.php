@extends('core::layouts.app')
@section('title', 'Student Profile')
@section('page_title', $student->user->name ?? 'Student Profile')

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endpush

@section('content')
@php
    $photoUrl = $student->photo ? asset($student->photo) : null;
    $fullName = $student->user->name ?? '—';
    $initials = strtoupper(substr($fullName, 0, 1));
    $currentEnrollments = $student->enrollments->filter(fn ($e) => $e->status === 'enrolled');
    $academicYear = $student->admission_date ? $student->admission_date->year : now()->year;
    // QR payload: identifier the scanner resolves to this student
    $qrPayload = $student->nsi_number ?: ($student->student_id . '@' . app('institution')->domain);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-6">

    {{-- ═══════════════ LEFT SIDEBAR — identity + QR ═══════════════ --}}
    <aside class="space-y-4">
        {{-- Identity card --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="h-20 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
            <div class="px-5 pb-5 -mt-10 text-center">
                @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $fullName }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow mx-auto bg-slate-200">
                @else
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 border-4 border-white shadow mx-auto flex items-center justify-center text-white text-3xl font-bold">
                        {{ $initials }}
                    </div>
                @endif
                <h2 class="mt-3 text-lg font-semibold text-slate-900">{{ $fullName }}</h2>
                <p class="text-xs text-slate-500">{{ $student->user->email ?? '' }}</p>
                <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-xs capitalize
                    {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $student->status }}
                </span>
            </div>

            <dl class="border-t border-slate-100 divide-y divide-slate-100 text-sm">
                <div class="flex justify-between px-5 py-2.5">
                    <dt class="text-slate-500">Matric No</dt>
                    <dd class="font-mono text-xs text-slate-900">{{ $student->student_id }}</dd>
                </div>
                @if($student->nsi_number)
                <div class="flex justify-between px-5 py-2.5">
                    <dt class="text-slate-500">NSI</dt>
                    <dd class="font-mono text-xs text-blue-700 font-semibold">{{ $student->nsi_number }}</dd>
                </div>
                @endif
                <div class="flex justify-between px-5 py-2.5">
                    <dt class="text-slate-500">Program</dt>
                    <dd class="text-slate-900 text-right">{{ $student->program->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between px-5 py-2.5">
                    <dt class="text-slate-500">Level</dt>
                    <dd class="text-slate-900 capitalize">{{ $student->program->level ?? '—' }}</dd>
                </div>
                <div class="flex justify-between px-5 py-2.5">
                    <dt class="text-slate-500">Year</dt>
                    <dd class="text-slate-900">Year {{ $student->current_year ?? 1 }} / Sem {{ $student->current_semester ?? 1 }}</dd>
                </div>
                <div class="flex justify-between px-5 py-2.5">
                    <dt class="text-slate-500">Gender</dt>
                    <dd class="text-slate-900 capitalize">{{ $student->gender ?? '—' }}</dd>
                </div>
            </dl>

            <div class="px-5 pb-5 pt-3 flex gap-2">
                <a href="{{ route('students.edit', $student) }}" class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs text-center rounded-lg">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('students.index') }}" class="flex-1 px-3 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs text-center rounded-lg">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        {{-- QR code for attendance --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 text-center">
            <h3 class="text-sm font-semibold text-slate-900 mb-1">
                <i class="fas fa-qrcode mr-1 text-indigo-600"></i> Attendance QR
            </h3>
            <p class="text-xs text-slate-500 mb-3">Scan with the campus attendance app</p>
            <div id="student-qr" class="flex justify-center items-center mb-2" data-payload="{{ $qrPayload }}"></div>
            <p class="text-xs font-mono text-slate-600 mt-2">{{ $qrPayload }}</p>
        </div>

        @if($student->nsi_number)
        {{-- National identity block (only when national ID mode is set) --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-900 mb-2">
                <i class="fas fa-id-card mr-1 text-emerald-600"></i> National Registry
            </h3>
            <p class="text-xs text-slate-500 mb-2">This student is registered with HEMIS.</p>
            <a href="https://{{ preg_replace('#^https?://|/.*$#', '', config('app.url')) }}/hemis/students/{{ $student->nsi_number }}" target="_blank"
               class="text-xs text-blue-600 hover:underline">
                View national profile ↗
            </a>
        </div>
        @endif
    </aside>

    {{-- ═══════════════ RIGHT — tabbed content ═══════════════ --}}
    <section class="space-y-4" x-data="{ tab: 'profile' }">
        {{-- Tab nav --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
            <nav class="flex divide-x divide-slate-100 text-sm">
                @php
                    $tabs = [
                        ['id'=>'profile',    'label'=>'Profile',     'icon'=>'fa-user'],
                        ['id'=>'academic',   'label'=>'Academic',    'icon'=>'fa-graduation-cap'],
                        ['id'=>'courses',    'label'=>'Courses',     'icon'=>'fa-book', 'badge'=>$currentEnrollments->count()],
                        ['id'=>'grades',     'label'=>'Grades',      'icon'=>'fa-chart-line', 'badge'=>$student->grades->count()],
                        ['id'=>'finance',    'label'=>'Finance',     'icon'=>'fa-receipt', 'badge'=>$student->invoices->count()],
                        ['id'=>'attendance', 'label'=>'Attendance',  'icon'=>'fa-calendar-check'],
                        ['id'=>'documents',  'label'=>'Documents',   'icon'=>'fa-folder-open'],
                        ['id'=>'timeline',   'label'=>'Timeline',    'icon'=>'fa-history'],
                    ];
                @endphp
                @foreach($tabs as $t)
                    <button type="button" @click="tab = '{{ $t['id'] }}'"
                            :class="tab === '{{ $t['id'] }}' ? 'bg-blue-50 text-blue-700 border-b-2 border-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 border-b-2 border-transparent'"
                            class="flex-shrink-0 flex items-center gap-2 px-4 py-3 font-medium whitespace-nowrap">
                        <i class="fas {{ $t['icon'] }}"></i>
                        <span>{{ $t['label'] }}</span>
                        @isset($t['badge'])
                            <span class="px-1.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs">{{ $t['badge'] }}</span>
                        @endisset
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- TAB: Profile --}}
        <div x-show="tab === 'profile'" class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Personal Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6 text-sm">
                @php
                    $profileRows = [
                        ['Full name',       $fullName],
                        ['Email',           $student->user->email ?? '—'],
                        ['Phone',           $student->phone ?? '—'],
                        ['Date of birth',   $student->date_of_birth?->format('d M Y') ?? '—'],
                        ['Age',             $student->date_of_birth ? $student->date_of_birth->age . ' yrs' : '—'],
                        ['Gender',          ucfirst($student->gender ?? '—')],
                        ['Nationality',     $student->nationality ?? '—'],
                        ['Blood group',     $student->blood_group ?? '—'],
                        ['Admission date',  $student->admission_date?->format('d M Y') ?? '—'],
                        ['Status',          ucfirst($student->status)],
                    ];
                @endphp
                @foreach($profileRows as [$label, $value])
                    <div>
                        <dt class="text-xs text-slate-500 uppercase tracking-wide">{{ $label }}</dt>
                        <dd class="text-slate-900 mt-0.5">{{ $value }}</dd>
                    </div>
                @endforeach
                <div class="md:col-span-2">
                    <dt class="text-xs text-slate-500 uppercase tracking-wide">Current address</dt>
                    <dd class="text-slate-900 mt-0.5">{{ $student->address ?? '—' }}</dd>
                </div>
            </dl>

            @if ($student->parents || $student->guardian_name ?? false)
                <h3 class="text-base font-semibold text-slate-900 mt-6 mb-4 pt-4 border-t border-slate-100">Parents / Guardian</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6 text-sm">
                    <div>
                        <dt class="text-xs text-slate-500 uppercase tracking-wide">Father</dt>
                        <dd class="text-slate-900 mt-0.5">{{ $student->parents->fathers_name ?? $student->fathers_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 uppercase tracking-wide">Mother</dt>
                        <dd class="text-slate-900 mt-0.5">{{ $student->parents->mothers_name ?? $student->mothers_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 uppercase tracking-wide">Guardian</dt>
                        <dd class="text-slate-900 mt-0.5">{{ $student->guardian_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 uppercase tracking-wide">Guardian phone</dt>
                        <dd class="text-slate-900 mt-0.5">{{ $student->guardian_phone ?? '—' }}</dd>
                    </div>
                </dl>
            @endif
        </div>

        {{-- TAB: Academic --}}
        <div x-show="tab === 'academic'" x-cloak class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Academic Affiliation</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6 text-sm">
                <div>
                    <dt class="text-xs text-slate-500 uppercase tracking-wide">Institution</dt>
                    <dd class="text-slate-900 mt-0.5">{{ app('institution')->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 uppercase tracking-wide">Faculty</dt>
                    <dd class="text-slate-900 mt-0.5">{{ $student->program->department->faculty->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 uppercase tracking-wide">Department</dt>
                    <dd class="text-slate-900 mt-0.5">{{ $student->program->department->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 uppercase tracking-wide">Program</dt>
                    <dd class="text-slate-900 mt-0.5">{{ $student->program->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 uppercase tracking-wide">Level</dt>
                    <dd class="text-slate-900 mt-0.5 capitalize">{{ $student->program->level ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 uppercase tracking-wide">Duration</dt>
                    <dd class="text-slate-900 mt-0.5">{{ $student->program->duration_years ?? '—' }} years</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 uppercase tracking-wide">Current year / semester</dt>
                    <dd class="text-slate-900 mt-0.5">Year {{ $student->current_year ?? 1 }}, Semester {{ $student->current_semester ?? 1 }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 uppercase tracking-wide">CGPA</dt>
                    <dd class="text-slate-900 mt-0.5 font-semibold">
                        {{ $student->cgpaRecords->sortByDesc('created_at')->first()->cgpa ?? '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- TAB: Courses --}}
        <div x-show="tab === 'courses'" x-cloak class="bg-white rounded-xl border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Enrolled courses</h3>
                <span class="text-xs text-slate-500">{{ $student->enrollments->count() }} total</span>
            </div>
            @if ($student->enrollments->count())
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                        <tr>
                            <th class="text-left px-6 py-3">Code</th>
                            <th class="text-left px-6 py-3">Course</th>
                            <th class="text-left px-6 py-3">Credits</th>
                            <th class="text-left px-6 py-3">Semester</th>
                            <th class="text-left px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($student->enrollments as $e)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3 font-mono text-xs">{{ $e->courseSection->course->code ?? '—' }}</td>
                                <td class="px-6 py-3 text-slate-900">{{ $e->courseSection->course->name ?? '—' }}</td>
                                <td class="px-6 py-3">{{ $e->courseSection->course->credits ?? '—' }}</td>
                                <td class="px-6 py-3">{{ $e->courseSection->semester->name ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs capitalize
                                        {{ $e->status === 'enrolled' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $e->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center text-sm text-slate-500">
                    <i class="fas fa-book text-3xl text-slate-300 mb-2"></i>
                    <p>No courses enrolled yet.</p>
                </div>
            @endif
        </div>

        {{-- TAB: Grades --}}
        <div x-show="tab === 'grades'" x-cloak class="bg-white rounded-xl border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-900">Grade records</h3>
            </div>
            @if ($student->grades->count())
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                        <tr>
                            <th class="text-left px-6 py-3">Course</th>
                            <th class="text-left px-6 py-3">Score</th>
                            <th class="text-left px-6 py-3">Grade</th>
                            <th class="text-left px-6 py-3">GP</th>
                            <th class="text-left px-6 py-3">Recorded</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($student->grades as $g)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3 text-slate-900">{{ $g->enrollment->courseSection->course->name ?? '—' }}</td>
                                <td class="px-6 py-3">{{ $g->marks_obtained ?? $g->score ?? '—' }}</td>
                                <td class="px-6 py-3 font-semibold">{{ $g->letter_grade ?? '—' }}</td>
                                <td class="px-6 py-3">{{ $g->grade_point ?? '—' }}</td>
                                <td class="px-6 py-3 text-xs text-slate-500">{{ $g->created_at?->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center text-sm text-slate-500">
                    <i class="fas fa-chart-line text-3xl text-slate-300 mb-2"></i>
                    <p>No grades recorded yet.</p>
                </div>
            @endif
        </div>

        {{-- TAB: Finance --}}
        <div x-show="tab === 'finance'" x-cloak class="bg-white rounded-xl border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Fee invoices &amp; payments</h3>
                @php
                    $totalBalance = $student->invoices->sum('balance');
                    $totalPaid = $student->invoices->sum('paid_amount');
                @endphp
                <div class="text-right text-sm">
                    <div>Paid: <strong class="text-emerald-700">{{ number_format($totalPaid, 2) }}</strong></div>
                    <div>Balance: <strong class="text-{{ $totalBalance > 0 ? 'red' : 'slate' }}-700">{{ number_format($totalBalance, 2) }}</strong></div>
                </div>
            </div>
            @if ($student->invoices->count())
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                        <tr>
                            <th class="text-left px-6 py-3">Invoice #</th>
                            <th class="text-left px-6 py-3">Semester</th>
                            <th class="text-right px-6 py-3">Total</th>
                            <th class="text-right px-6 py-3">Paid</th>
                            <th class="text-right px-6 py-3">Balance</th>
                            <th class="text-left px-6 py-3">Due</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($student->invoices as $inv)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3 font-mono text-xs">#{{ $inv->id }}</td>
                                <td class="px-6 py-3">{{ $inv->semester->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-right">{{ number_format($inv->total_amount, 2) }}</td>
                                <td class="px-6 py-3 text-right text-emerald-700">{{ number_format($inv->paid_amount, 2) }}</td>
                                <td class="px-6 py-3 text-right {{ $inv->balance > 0 ? 'text-red-700 font-semibold' : 'text-slate-500' }}">{{ number_format($inv->balance, 2) }}</td>
                                <td class="px-6 py-3 text-xs">{{ $inv->due_date?->format('d M Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center text-sm text-slate-500">
                    <i class="fas fa-receipt text-3xl text-slate-300 mb-2"></i>
                    <p>No invoices yet.</p>
                </div>
            @endif
        </div>

        {{-- TAB: Attendance (placeholder — counts only) --}}
        <div x-show="tab === 'attendance'" x-cloak class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Attendance overview</h3>
            <p class="text-sm text-slate-500">
                Attendance reporting is wired through the <strong>QR code attendance</strong> system. Use the
                QR panel on the left with the campus attendance app to check students in, or assign this
                student to a class and view the full attendance report.
            </p>
            <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                <div class="p-4 bg-emerald-50 rounded-lg">
                    <div class="text-2xl font-bold text-emerald-700">—</div>
                    <div class="text-xs text-emerald-600 mt-1">Present days</div>
                </div>
                <div class="p-4 bg-amber-50 rounded-lg">
                    <div class="text-2xl font-bold text-amber-700">—</div>
                    <div class="text-xs text-amber-600 mt-1">Late</div>
                </div>
                <div class="p-4 bg-red-50 rounded-lg">
                    <div class="text-2xl font-bold text-red-700">—</div>
                    <div class="text-xs text-red-600 mt-1">Absent</div>
                </div>
            </div>
        </div>

        {{-- TAB: Documents (placeholder) --}}
        <div x-show="tab === 'documents'" x-cloak class="bg-white rounded-xl border border-slate-200 p-8 text-center">
            <i class="fas fa-folder-open text-3xl text-slate-300 mb-2"></i>
            <p class="text-sm text-slate-500">Document upload coming soon.</p>
        </div>

        {{-- TAB: Timeline --}}
        <div x-show="tab === 'timeline'" x-cloak class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Activity timeline</h3>
            <ol class="space-y-4 text-sm">
                <li class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-plus text-xs"></i>
                    </div>
                    <div>
                        <div class="font-medium text-slate-900">Student admitted</div>
                        <div class="text-xs text-slate-500">{{ $student->admission_date?->format('d M Y') ?? 'Date unknown' }}</div>
                    </div>
                </li>
                @foreach($student->grades->sortByDesc('created_at')->take(5) as $g)
                <li class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-chart-line text-xs"></i>
                    </div>
                    <div>
                        <div class="font-medium text-slate-900">Grade recorded: {{ $g->letter_grade ?? '—' }} in {{ $g->enrollment->courseSection->course->name ?? '—' }}</div>
                        <div class="text-xs text-slate-500">{{ $g->created_at?->format('d M Y') }}</div>
                    </div>
                </li>
                @endforeach
            </ol>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('student-qr');
    if (el && window.QRCode) {
        new QRCode(el, {
            text: el.dataset.payload,
            width: 160,
            height: 160,
            correctLevel: QRCode.CorrectLevel.M
        });
    }
});
</script>
@endsection
