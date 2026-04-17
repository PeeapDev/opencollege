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
</head>
<body class="bg-gray-50 min-h-screen">
<nav class="bg-white shadow-sm">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center"><i class="fas fa-graduation-cap text-white"></i></div>
            <span class="font-bold text-gray-900">{{ $institution->name }}</span>
        </div>
        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">Login</a>
    </div>
</nav>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Online Application</h1>
        <p class="text-gray-500 mt-2">{{ $settings->academic_year ?? date('Y').'/'.( date('Y')+1) }} Academic Year</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-6 bg-green-50 border border-green-200 rounded-xl text-center">
        <i class="fas fa-check-circle text-green-600 text-3xl mb-3"></i>
        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
        <p class="text-green-600 text-sm mt-2">You will be contacted via email about your application status.</p>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
        <ul class="text-sm text-red-700 space-y-1">@foreach($errors->all() as $e)<li><i class="fas fa-exclamation-circle mr-1"></i>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    @if($settings->instructions)
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800">
        <h3 class="font-semibold mb-1"><i class="fas fa-info-circle mr-1"></i> Instructions</h3>
        <p>{{ $settings->instructions }}</p>
    </div>
    @endif

    {{-- ═══════════════ NSI LOOKUP PANEL ═══════════════ --}}
    <div class="mb-6 bg-white rounded-xl border overflow-hidden" x-data="nsiApply()">
        {{-- Tabs --}}
        <div class="flex border-b bg-slate-50">
            <button type="button" @click="mode='nsi'"
                    :class="mode==='nsi' ? 'bg-white text-blue-600 border-b-2 border-blue-600' : 'text-slate-600 hover:text-slate-900'"
                    class="flex-1 px-4 py-3 text-sm font-medium transition">
                <i class="fas fa-id-card mr-1"></i> Apply with NSI
                <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Recommended</span>
            </button>
            <button type="button" @click="mode='manual'"
                    :class="mode==='manual' ? 'bg-white text-blue-600 border-b-2 border-blue-600' : 'text-slate-600 hover:text-slate-900'"
                    class="flex-1 px-4 py-3 text-sm font-medium transition">
                <i class="fas fa-keyboard mr-1"></i> Apply Manually
            </button>
        </div>

        {{-- NSI tab --}}
        <div x-show="mode==='nsi'" class="p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-1">Check your eligibility using your NSI</h3>
            <p class="text-sm text-slate-500 mb-4">
                If you have a National Student Identifier from secondary school, enter it below. We'll fetch
                your records and verify that you meet the WASSCE (SSS 3) prerequisite.
            </p>

            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" x-model="nsi" @keydown.enter.prevent="check"
                       placeholder="SL-2025-09-00001"
                       class="flex-1 px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">
                <button type="button" @click="check" :disabled="loading || !nsi.trim()"
                        class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 disabled:bg-slate-400 disabled:cursor-not-allowed">
                    <span x-show="!loading"><i class="fas fa-search mr-1"></i> Check Eligibility</span>
                    <span x-show="loading"><i class="fas fa-spinner fa-spin mr-1"></i> Checking…</span>
                </button>
            </div>

            {{-- Error / not found --}}
            <template x-if="error">
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
                    <i class="fas fa-exclamation-circle mr-1"></i> <span x-text="error"></span>
                </div>
            </template>

            {{-- Result card --}}
            <template x-if="result">
                <div class="mt-4 border rounded-xl overflow-hidden"
                     :class="result.eligible ? 'border-emerald-300' : 'border-amber-300'">
                    <div class="px-5 py-3 flex items-center gap-3"
                         :class="result.eligible ? 'bg-emerald-50' : 'bg-amber-50'">
                        <i class="fas text-2xl"
                           :class="result.eligible ? 'fa-circle-check text-emerald-600' : 'fa-triangle-exclamation text-amber-600'"></i>
                        <div>
                            <div class="font-semibold"
                                 :class="result.eligible ? 'text-emerald-800' : 'text-amber-800'"
                                 x-text="result.eligible ? 'Eligible to apply' : 'Not yet eligible'"></div>
                            <div class="text-xs"
                                 :class="result.eligible ? 'text-emerald-700' : 'text-amber-700'"
                                 x-text="result.reason"></div>
                        </div>
                    </div>

                    <div class="p-5 flex gap-4 items-start">
                        <template x-if="result.student.photo_url">
                            <img :src="result.student.photo_url" alt="" class="w-20 h-20 rounded-lg object-cover border border-slate-200 flex-shrink-0">
                        </template>
                        <template x-if="!result.student.photo_url">
                            <div class="w-20 h-20 rounded-lg bg-slate-200 flex items-center justify-center text-slate-400 flex-shrink-0">
                                <i class="fas fa-user text-2xl"></i>
                            </div>
                        </template>
                        <div class="flex-1 text-sm">
                            <div class="font-semibold text-slate-900 text-base" x-text="result.student.name"></div>
                            <div class="text-slate-500 text-xs mt-0.5" x-text="result.student.school_name || 'School: —'"></div>
                            <dl class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                                <div><dt class="inline text-slate-500">Current class:</dt> <dd class="inline font-semibold" x-text="result.student.current_class || '—'"></dd></div>
                                <div><dt class="inline text-slate-500">Gender:</dt> <dd class="inline capitalize" x-text="result.student.gender || '—'"></dd></div>
                                <div><dt class="inline text-slate-500">DOB:</dt> <dd class="inline" x-text="result.student.date_of_birth || '—'"></dd></div>
                                <div><dt class="inline text-slate-500">Aggregate:</dt> <dd class="inline" x-text="result.student.aggregate ?? '—'"></dd></div>
                            </dl>
                        </div>
                    </div>

                    {{-- Eligible: prefill CTA --}}
                    <template x-if="result.eligible">
                        <div class="px-5 pb-5">
                            <button type="button" @click="prefillAndSwitch"
                                    class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-arrow-right mr-1"></i> Continue — use these details to apply
                            </button>
                        </div>
                    </template>

                    {{-- Ineligible --}}
                    <template x-if="!result.eligible">
                        <div class="px-5 pb-5 text-sm text-amber-800 bg-amber-50 border-t border-amber-200 pt-3">
                            You must complete <strong>WASSCE (Senior Secondary 3)</strong> before you can apply for
                            tertiary admission. Your current class is
                            <strong x-text="result.student.current_class || 'unknown'"></strong>. Return here after
                            you finish secondary school.
                        </div>
                    </template>
                </div>
            </template>

            <p class="mt-4 text-xs text-slate-500">
                Don't have an NSI?
                <button type="button" @click="mode='manual'" class="text-blue-600 hover:underline font-medium">Apply manually instead →</button>
            </p>
        </div>

        {{-- Manual tab banner --}}
        <div x-show="mode==='manual'" class="p-4 bg-blue-50 border-b text-sm text-blue-900">
            <i class="fas fa-info-circle mr-1"></i>
            Filling the form manually. If you have an NSI,
            <button type="button" @click="mode='nsi'" class="underline font-medium">use it to verify eligibility first</button>.
        </div>
    </div>

    <script>
        function nsiApply() {
            return {
                mode: 'nsi',
                nsi: '',
                loading: false,
                result: null,
                error: null,
                async check() {
                    this.error = null;
                    this.result = null;
                    if (!this.nsi.trim()) return;
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('admission.check-nsi') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ nsi: this.nsi.trim() })
                        });
                        const data = await res.json();
                        if (data.ok) {
                            this.result = data;
                        } else {
                            this.error = data.message || 'Lookup failed.';
                        }
                    } catch (e) {
                        this.error = 'Network error — please try again.';
                    } finally {
                        this.loading = false;
                    }
                },
                prefillAndSwitch() {
                    if (!this.result || !this.result.student) return;
                    const s = this.result.student;
                    const f = document.forms[0];
                    const set = (name, value) => {
                        const el = f && f.elements[name];
                        if (el && value != null && value !== '') el.value = value;
                    };
                    set('first_name', s.first_name);
                    set('last_name', s.last_name);
                    set('email', s.email);
                    set('phone', s.phone);
                    set('date_of_birth', s.date_of_birth ? (s.date_of_birth.substring(0, 10)) : '');
                    set('gender', (s.gender || '').toLowerCase());
                    set('nsi_number', this.result.nsi);
                    this.mode = 'manual';
                    setTimeout(() => f?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
                },
            };
        }
    </script>

    <form method="POST" action="{{ route('admission.submit') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="institution_id" value="{{ $institution->id }}">

        <div class="bg-white rounded-xl border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-user mr-2 text-blue-600"></i>Personal Information</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>
            <div class="grid md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="+232...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>
            <div class="grid md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                    <select name="gender" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select...</option>
                        <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                        <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                    <input type="text" name="nationality" value="{{ old('nationality', 'Sierra Leonean') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NSI Number</label>
                    <input type="text" name="nsi_number" value="{{ old('nsi_number') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="High school NSI">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" name="address" value="{{ old('address') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            <div class="grid md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program *</label>
                    <select name="program_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select program...</option>
                        @foreach($programs as $p)<option value="{{ $p->id }}" {{ old('program_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-user-friends mr-2 text-green-600"></i>Guardian Information</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Name</label>
                    <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Phone</label>
                    <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Email</label>
                    <input type="email" name="guardian_email" value="{{ old('guardian_email') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                    <select name="guardian_relation" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select...</option>
                        @foreach(['Parent','Father','Mother','Guardian','Sibling','Spouse','Other'] as $r)<option value="{{ strtolower($r) }}" {{ old('guardian_relation')==strtolower($r)?'selected':'' }}>{{ $r }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="px-10 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                <i class="fas fa-paper-plane mr-2"></i>Submit Application
            </button>
        </div>
    </form>
</div>

<footer class="text-center py-6 text-xs text-gray-400">
    &copy; {{ date('Y') }} {{ $institution->name }} — Powered by OpenCollege
</footer>
</body>
</html>
