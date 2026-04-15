<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — OpenCollege</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">

<div class="min-h-full flex">
    {{-- Left side - Branding --}}
    <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-12">
        <div class="max-w-md text-center">
            <div class="w-24 h-24 bg-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl">
                <i class="fas fa-graduation-cap text-white text-4xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-4">OpenCollege</h1>
            <p class="text-blue-200 text-lg mb-8">Open Source College & University Management System</p>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <i class="fas fa-users text-blue-300 text-2xl mb-2"></i>
                    <p class="text-white text-sm font-medium">Students</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <i class="fas fa-book-open text-blue-300 text-2xl mb-2"></i>
                    <p class="text-white text-sm font-medium">Courses</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <i class="fas fa-chart-line text-blue-300 text-2xl mb-2"></i>
                    <p class="text-white text-sm font-medium">Analytics</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right side - Login form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-graduation-cap text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">OpenCollege</h1>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
                    <p class="text-gray-500 mt-1">Sign in to your account</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-center gap-2 text-green-700 text-sm"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center gap-2 text-red-700 text-sm">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first() }}
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1.5">Email, Phone, NSI or Matric No.</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}" required autofocus
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Email, phone, NSI or matric number">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div class="relative" x-data="{ show: false }">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required
                                   class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Enter your password">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Remember me
                        </label>
                        <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit"
                            class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>
            </div>

            <div class="text-center mt-6 space-y-3">
                <a href="{{ route('college.register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 text-white text-sm font-medium rounded-xl transition">
                    <i class="fas fa-school"></i> Register Your College
                </a>
                <p class="text-sm text-blue-300/60">OpenCollege v1.0 &copy; {{ date('Y') }} <a href="https://github.com/PeeapDev" class="underline">PeeapDev</a></p>
            </div>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
