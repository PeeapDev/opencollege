<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Apply — {{ $institution->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-50 min-h-screen">

<nav class="bg-white shadow-sm">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-graduation-cap text-white"></i>
            </div>
            <span class="font-bold text-slate-900">{{ $institution->name }}</span>
        </div>
        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">Login</a>
    </div>
</nav>

<div class="max-w-3xl mx-auto px-4 py-8" x-data="applyFlow()" x-cloak>

    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Apply to {{ $institution->name }}</h1>
        <p class="text-slate-500 mt-2">{{ $settings->academic_year ?? date('Y').'/'.(date('Y')+1) }} Academic Year</p>
    </div>

    {{-- Tab selector (hidden once NSI flow advances past step 1) --}}
    <div x-show="step==='lookup' || mode==='manual'" class="flex border-b border-slate-200 mb-6 bg-white rounded-t-2xl overflow-hidden">
        <button type="button" @click="setMode('nsi')"
                :class="mode==='nsi' ? 'bg-blue-50 text-blue-700 border-b-2 border-blue-600' : 'text-slate-600 hover:text-slate-900 border-b-2 border-transparent'"
                class="flex-1 px-4 py-4 text-sm font-semibold transition">
            <i class="fas fa-id-card mr-1"></i> Apply with NSI
            <span class="ml-1 text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 align-middle">Fastest</span>
        </button>
        <button type="button" @click="setMode('manual')"
                :class="mode==='manual' ? 'bg-blue-50 text-blue-700 border-b-2 border-blue-600' : 'text-slate-600 hover:text-slate-900 border-b-2 border-transparent'"
                class="flex-1 px-4 py-4 text-sm font-semibold transition">
            <i class="fas fa-keyboard mr-1"></i> Apply Manually
            <span class="ml-1 text-[10px] text-slate-500 align-middle">No NSI?</span>
        </button>
    </div>

    {{-- ══════ NSI TAB — STEP 1: lookup input ══════ --}}
    <div x-show="mode==='nsi' && step==='lookup'" class="bg-white rounded-2xl border shadow-sm p-8">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-id-card text-blue-600"></i>
            </div>
            <h2 class="text-lg font-semibold text-slate-900">Enter your NSI</h2>
        </div>
        <p class="text-sm text-slate-500 mb-6">
            Format: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">SL-YYYY-MM-NNNNN</code>.
            You received this when you joined your secondary school.
        </p>

        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" x-model="nsi" @keydown.enter.prevent="check"
                   placeholder="SL-2025-09-00001" autofocus
                   class="flex-1 px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-base">
            <button type="button" @click="check" :disabled="loading || !nsi.trim()"
                    class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 disabled:bg-slate-400 disabled:cursor-not-allowed whitespace-nowrap">
                <span x-show="!loading"><i class="fas fa-search mr-1"></i> Look up record</span>
                <span x-show="loading"><i class="fas fa-spinner fa-spin mr-1"></i> Searching…</span>
            </button>
        </div>

        <template x-if="error">
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800">
                <i class="fas fa-exclamation-circle mr-1"></i> <span x-text="error"></span>
            </div>
        </template>

        <div class="mt-8 pt-6 border-t text-xs text-slate-500 space-y-1">
            <div><i class="fas fa-lock text-slate-400 mr-1"></i> Your data is fetched directly from the national registry — nothing is shared with third parties.</div>
            <div><i class="fas fa-shield-halved text-slate-400 mr-1"></i> Your application is only reviewed by the admissions office at {{ $institution->name }}.</div>
        </div>
    </div>

    {{-- ══════ STEP 2 — Record + confirmation ══════ --}}
    <template x-if="mode==='nsi' && step==='review' && result">
        <div>
            {{-- Eligibility banner --}}
            <div class="rounded-2xl overflow-hidden border mb-5"
                 :class="result.eligible ? 'border-emerald-300' : 'border-amber-300'">
                <div class="px-6 py-4 flex items-center gap-3"
                     :class="result.eligible ? 'bg-emerald-50' : 'bg-amber-50'">
                    <i class="fas text-3xl"
                       :class="result.eligible ? 'fa-circle-check text-emerald-600' : 'fa-triangle-exclamation text-amber-600'"></i>
                    <div>
                        <div class="font-bold text-lg"
                             :class="result.eligible ? 'text-emerald-800' : 'text-amber-800'"
                             x-text="result.eligible ? 'Eligible to apply' : 'Not yet eligible'"></div>
                        <div class="text-sm"
                             :class="result.eligible ? 'text-emerald-700' : 'text-amber-700'"
                             x-text="result.reason"></div>
                    </div>
                </div>
            </div>

            {{-- Student profile --}}
            <div class="bg-white rounded-2xl border shadow-sm overflow-hidden mb-5">
                <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
                <div class="px-6 pb-6 -mt-14">
                    <div class="flex items-start gap-5">
                        <template x-if="result.student.photo_url">
                            <img :src="result.student.photo_url"
                                 class="w-28 h-28 rounded-2xl object-cover border-4 border-white shadow-lg bg-slate-200">
                        </template>
                        <template x-if="!result.student.photo_url">
                            <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 border-4 border-white shadow-lg flex items-center justify-center">
                                <span class="text-white text-4xl font-bold"
                                      x-text="(result.student.name || '?').charAt(0).toUpperCase()"></span>
                            </div>
                        </template>
                        <div class="flex-1 pt-14">
                            <h2 class="text-xl font-bold text-slate-900" x-text="result.student.name"></h2>
                            <p class="text-sm text-slate-500 mt-0.5">
                                <i class="fas fa-school text-slate-400 mr-1"></i>
                                <span x-text="result.student.school_name || 'School — unknown'"></span>
                            </p>
                            <p class="text-xs font-mono text-blue-600 mt-1" x-text="'NSI: ' + result.nsi"></p>
                        </div>
                    </div>

                    <dl class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-y-3 gap-x-6 text-sm pt-4 border-t border-slate-100">
                        <div>
                            <dt class="text-xs text-slate-500 uppercase">Current class</dt>
                            <dd class="text-slate-900 font-semibold mt-0.5" x-text="result.student.current_class || '—'"></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500 uppercase">Gender</dt>
                            <dd class="text-slate-900 capitalize mt-0.5" x-text="result.student.gender || '—'"></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500 uppercase">Date of birth</dt>
                            <dd class="text-slate-900 mt-0.5" x-text="(result.student.date_of_birth || '—').substring(0,10)"></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500 uppercase">Phone</dt>
                            <dd class="text-slate-900 mt-0.5" x-text="result.student.phone || '—'"></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500 uppercase">Email</dt>
                            <dd class="text-slate-900 mt-0.5 truncate" x-text="result.student.email || 'not recorded'"></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500 uppercase">Aggregate score</dt>
                            <dd class="text-slate-900 mt-0.5" x-text="result.student.aggregate != null ? result.student.aggregate : 'n/a'"></dd>
                        </div>
                    </dl>

                    {{-- Academic history preview --}}
                    <template x-if="result.student.academic_history && result.student.academic_history.length">
                        <div class="mt-5 pt-4 border-t border-slate-100">
                            <h4 class="text-xs font-semibold uppercase text-slate-500 mb-2">Academic History</h4>
                            <div class="space-y-1">
                                <template x-for="h in result.student.academic_history.slice(0,5)" :key="h.class + (h.academic_year || '')">
                                    <div class="flex items-center justify-between text-xs py-1">
                                        <span>
                                            <span class="font-medium text-slate-800" x-text="h.class"></span>
                                            <span class="text-slate-400" x-show="h.academic_year">· <span x-text="h.academic_year"></span></span>
                                        </span>
                                        <span class="text-slate-500" x-text="h.has_results ? (h.results.length + ' exam results') : (h.is_promoted ? 'promoted' : '—')"></span>
                                    </div>
                                </template>
                                <p class="text-[11px] text-slate-400 pt-1" x-show="result.student.academic_history.length > 5"
                                   x-text="'+ ' + (result.student.academic_history.length - 5) + ' earlier entries'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Eligible: program pick + submit --}}
            <template x-if="result.eligible">
                <div class="bg-white rounded-2xl border shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-1">Choose your program</h3>
                    <p class="text-sm text-slate-500 mb-4">
                        Select the program you'd like to apply to at {{ $institution->name }}. The admissions office will review
                        your national record and notify you by phone.
                    </p>
                    <select x-model="programId"
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">— select a program —</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst($p->level) }}, {{ $p->duration_years }}y)</option>
                        @endforeach
                    </select>

                    <template x-if="submitError">
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700">
                            <i class="fas fa-exclamation-circle mr-1"></i> <span x-text="submitError"></span>
                        </div>
                    </template>

                    <div class="flex flex-col sm:flex-row gap-2 mt-5">
                        <button type="button" @click="back"
                                class="px-5 py-3 border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 text-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Not me — go back
                        </button>
                        <button type="button" @click="submit" :disabled="submitting || !programId"
                                class="flex-1 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-400 disabled:cursor-not-allowed text-white font-bold rounded-xl text-base">
                            <span x-show="!submitting">
                                <i class="fas fa-check mr-1"></i> This is me — Submit Application
                            </span>
                            <span x-show="submitting">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Submitting…
                            </span>
                        </button>
                    </div>
                </div>
            </template>

            {{-- Ineligible: explanation + try again --}}
            <template x-if="!result.eligible">
                <div class="bg-white rounded-2xl border border-amber-200 p-6 text-center">
                    <p class="text-slate-700 mb-4">
                        You must complete <strong>WASSCE (Senior Secondary 3)</strong> at your school before you can
                        apply for tertiary admission. Your current class on the national registry is
                        <strong x-text="result.student.current_class || 'unknown'"></strong>.
                    </p>
                    <p class="text-sm text-slate-500 mb-6">Return here once you've finished secondary school.</p>
                    <button type="button" @click="back"
                            class="px-6 py-2.5 bg-slate-600 text-white rounded-xl hover:bg-slate-700 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Try a different NSI
                    </button>
                </div>
            </template>
        </div>
    </template>

    {{-- ══════ STEP 3 — Success ══════ --}}
    <template x-if="mode==='nsi' && step==='done' && submitted">
        <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm p-10 text-center">
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-check text-emerald-600 text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-1">Application submitted</h2>
            <p class="text-slate-600 mb-5">
                <span x-show="!submitted.duplicate">
                    Thanks, <strong x-text="submitted.student && submitted.student.name"></strong>. The admissions office at
                    {{ $institution->name }} will review your national record.
                </span>
                <span x-show="submitted.duplicate" x-text="submitted.message"></span>
            </p>
            <div class="inline-block bg-slate-100 px-5 py-3 rounded-xl mb-5">
                <div class="text-xs text-slate-500">Application number</div>
                <div class="text-2xl font-mono font-bold text-slate-900" x-text="submitted.application_number"></div>
            </div>
            <p class="text-xs text-slate-500">Keep this number. You'll be contacted by phone when there's a decision.</p>
            <a href="{{ url('/') }}" class="inline-block mt-6 px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 text-sm">
                Back to homepage
            </a>
        </div>
    </template>

    {{-- ══════ MANUAL TAB — full form ══════ --}}
    <div x-show="mode==='manual'" class="bg-white rounded-2xl border shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-keyboard text-amber-600"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Apply without an NSI</h2>
                <p class="text-xs text-slate-500 mt-0.5">Fill the full form manually. Use this if you don't have an NSI yet.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800">
                <ul class="space-y-1">
                    @foreach($errors->all() as $e)<li><i class="fas fa-exclamation-circle mr-1"></i>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admission.submit') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="institution_id" value="{{ $institution->id }}">

            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-3"><i class="fas fa-user mr-1 text-blue-600"></i> Personal Information</h3>
                <div class="grid md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                </div>
                <div class="grid md:grid-cols-3 gap-3 mt-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+232..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                </div>
                <div class="grid md:grid-cols-3 gap-3 mt-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Gender *</label>
                        <select name="gender" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="">Select…</option>
                            <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                            <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>
                            <option value="other" {{ old('gender')=='other'?'selected':'' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nationality</label>
                        <input type="text" name="nationality" value="{{ old('nationality') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">National ID / Passport</label>
                        <input type="text" name="national_id" value="{{ old('national_id') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-3 mt-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Address</label>
                        <input type="text" name="address" value="{{ old('address') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-3"><i class="fas fa-graduation-cap mr-1 text-blue-600"></i> Program</h3>
                <select name="program_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    <option value="">Select program…</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ old('program_id')==$p->id?'selected':'' }}>{{ $p->name }} ({{ ucfirst($p->level) }}, {{ $p->duration_years }}y)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-3"><i class="fas fa-user-friends mr-1 text-emerald-600"></i> Guardian (optional)</h3>
                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Guardian Name</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Guardian Phone</label>
                        <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Guardian Email</label>
                        <input type="email" name="guardian_email" value="{{ old('guardian_email') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Relationship</label>
                        <select name="guardian_relation" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="">Select…</option>
                            <option value="father" {{ old('guardian_relation')=='father'?'selected':'' }}>Father</option>
                            <option value="mother" {{ old('guardian_relation')=='mother'?'selected':'' }}>Mother</option>
                            <option value="guardian" {{ old('guardian_relation')=='guardian'?'selected':'' }}>Guardian</option>
                            <option value="other" {{ old('guardian_relation')=='other'?'selected':'' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full px-5 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 text-base">
                <i class="fas fa-paper-plane mr-1"></i> Submit Application
            </button>
        </form>
    </div>
</div>

<script>
function applyFlow() {
    return {
        mode: '{{ session('errors') && count($errors) ? 'manual' : 'nsi' }}',  // sticky to manual if server-side validation errors
        step: 'lookup',
        nsi: '',
        programId: '',
        loading: false,
        result: null,
        error: null,
        submitting: false,
        submitted: null,
        submitError: null,

        async check() {
            this.error = null;
            this.result = null;
            if (!this.nsi.trim()) return;
            this.loading = true;
            try {
                const r = await fetch('{{ route('admission.check-nsi') }}', {
                    method: 'POST',
                    headers: this.jsonHeaders(),
                    body: JSON.stringify({ nsi: this.nsi.trim() })
                });
                const d = await r.json();
                if (d.ok) {
                    this.result = d;
                    this.step = 'review';
                } else {
                    this.error = d.message || 'Lookup failed.';
                }
            } catch (e) {
                this.error = 'Network error — please try again.';
            } finally {
                this.loading = false;
            }
        },

        async submit() {
            if (!this.result || !this.programId) return;
            this.submitError = null;
            this.submitting = true;
            try {
                const r = await fetch('{{ route('admission.submit-by-nsi') }}', {
                    method: 'POST',
                    headers: this.jsonHeaders(),
                    body: JSON.stringify({
                        nsi: this.result.nsi,
                        program_id: this.programId,
                        institution_id: {{ $institution->id }},
                    })
                });
                const d = await r.json();
                if (d.ok) {
                    this.submitted = d;
                    this.step = 'done';
                } else {
                    this.submitError = d.message || 'Could not submit application.';
                }
            } catch (e) {
                this.submitError = 'Network error — please try again.';
            } finally {
                this.submitting = false;
            }
        },

        back() {
            this.step = 'lookup';
            this.result = null;
            this.programId = '';
            this.submitError = null;
        },

        setMode(m) {
            this.mode = m;
            // Reset NSI flow if switching away then back
            if (m === 'nsi' && this.step === 'done') this.back();
        },

        jsonHeaders() {
            return {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest',
            };
        },
    };
}
</script>
</body>
</html>
