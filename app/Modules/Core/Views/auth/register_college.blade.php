<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your College — OpenCollege</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">

<div class="min-h-full flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-2xl">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl">
                <i class="fas fa-graduation-cap text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">Register Your College</h1>
            <p class="text-blue-200 mt-2">Join the OpenCollege platform — Free college management system for Sierra Leone</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 text-green-200 rounded-xl flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 text-red-200 rounded-xl">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('college.register') }}" class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/10" x-data="{ step: 1 }">
            @csrf

            {{-- Step indicator --}}
            <div class="flex items-center justify-center gap-3 mb-8">
                <button type="button" @click="step = 1" :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-white/10 text-white/50'" class="w-8 h-8 rounded-full text-sm font-bold transition">1</button>
                <div class="w-12 h-0.5" :class="step >= 2 ? 'bg-blue-500' : 'bg-white/10'"></div>
                <button type="button" @click="step = 2" :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-white/10 text-white/50'" class="w-8 h-8 rounded-full text-sm font-bold transition">2</button>
            </div>

            {{-- Step 1: College Details --}}
            <div x-show="step === 1" x-transition>
                <h3 class="text-white font-semibold text-lg mb-4"><i class="fas fa-school mr-2 text-blue-400"></i>College Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">College Name *</label>
                        <input type="text" name="college_name" value="{{ old('college_name') }}" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g. Fourah Bay College">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-blue-200 mb-1">Type *</label>
                            <select name="college_type" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="college" class="text-gray-900" {{ old('college_type') == 'college' ? 'selected' : '' }}>College</option>
                                <option value="polytechnic" class="text-gray-900" {{ old('college_type') == 'polytechnic' ? 'selected' : '' }}>Polytechnic</option>
                                <option value="university" class="text-gray-900" {{ old('college_type') == 'university' ? 'selected' : '' }}>University</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-blue-200 mb-1">Subdomain *</label>
                            <div class="flex">
                                <input type="text" name="domain" value="{{ old('domain') }}" required class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-l-xl text-white placeholder-white/40 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="fourahbay">
                                <span class="inline-flex items-center px-3 py-3 bg-white/5 border border-l-0 border-white/20 rounded-r-xl text-blue-300 text-xs">.college.edu.sl</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-blue-200 mb-1">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="info@college.edu.sl">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-blue-200 mb-1">Phone *</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="+232-xx-xxx-xxx">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-blue-200 mb-1">City</label>
                            <input type="text" name="city" value="{{ old('city', 'Freetown') }}" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-blue-200 mb-1">Address</label>
                            <input type="text" name="address" value="{{ old('address') }}" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <button type="button" @click="step = 2" class="w-full mt-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition">Next: Admin Account <i class="fas fa-arrow-right ml-2"></i></button>
                </div>
            </div>

            {{-- Step 2: Admin Account --}}
            <div x-show="step === 2" x-transition>
                <h3 class="text-white font-semibold text-lg mb-4"><i class="fas fa-user-shield mr-2 text-purple-400"></i>Administrator Account</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">Admin Full Name *</label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">Admin Email *</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="admin@college.edu.sl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">Password *</label>
                        <input type="password" name="admin_password" required minlength="8" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Min 8 characters">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1">Confirm Password *</label>
                        <input type="password" name="admin_password_confirmation" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex gap-3 mt-2">
                        <button type="button" @click="step = 1" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-medium rounded-xl transition"><i class="fas fa-arrow-left mr-2"></i>Back</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition"><i class="fas fa-rocket mr-2"></i>Register College</button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Footer --}}
        <div class="text-center mt-6">
            <p class="text-blue-300 text-sm">Already registered? <a href="{{ route('login') }}" class="text-white font-medium hover:underline">Sign in</a></p>
            <p class="text-blue-400/50 text-xs mt-3">OpenCollege v1.0 &copy; {{ date('Y') }} PeeapDev — Open Source</p>
        </div>
    </div>
</div>
</body>
</html>
