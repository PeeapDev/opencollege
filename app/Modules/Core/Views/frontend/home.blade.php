<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $institution->name }} — Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .hero-gradient { background: linear-gradient(135deg, {{ $settings->primary_color ?? '#2563eb' }} 0%, {{ $settings->secondary_color ?? '#1e40af' }} 100%); }
    </style>
</head>
<body class="bg-white">
{{-- Navbar --}}
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 hero-gradient rounded-xl flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-white"></i>
                </div>
                <span class="font-bold text-lg text-gray-900">{{ $institution->name }}</span>
            </div>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="{{ route('frontend.home') }}" class="text-blue-600">Home</a>
                <a href="{{ route('frontend.about') }}" class="hover:text-blue-600">About</a>
                <a href="{{ route('frontend.programs') }}" class="hover:text-blue-600">Programs</a>
                <a href="{{ route('frontend.contact') }}" class="hover:text-blue-600">Contact</a>
                @if($admissionOpen)
                <a href="{{ route('admission.apply') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Apply Now</a>
                @endif
                <a href="{{ route('login') }}" class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition">Login</a>
            </div>
            <button class="md:hidden text-gray-600" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
        <div id="mobileMenu" class="hidden md:hidden pb-4 space-y-2">
            <a href="{{ route('frontend.home') }}" class="block px-3 py-2 text-sm text-blue-600">Home</a>
            <a href="{{ route('frontend.about') }}" class="block px-3 py-2 text-sm text-gray-600">About</a>
            <a href="{{ route('frontend.programs') }}" class="block px-3 py-2 text-sm text-gray-600">Programs</a>
            <a href="{{ route('frontend.contact') }}" class="block px-3 py-2 text-sm text-gray-600">Contact</a>
            @if($admissionOpen)<a href="{{ route('admission.apply') }}" class="block px-3 py-2 text-sm text-green-600 font-medium">Apply Now</a>@endif
            <a href="{{ route('login') }}" class="block px-3 py-2 text-sm text-blue-600 font-medium">Login</a>
        </div>
    </div>
</nav>

{{-- Hero --}}
<section class="hero-gradient text-white py-20 lg:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <h1 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight">{{ $settings->hero_title ?? 'Welcome to ' . $institution->name }}</h1>
            <p class="text-lg text-blue-100 mb-8">{{ $settings->hero_subtitle ?? 'Empowering students with world-class education and cutting-edge research opportunities.' }}</p>
            <div class="flex flex-wrap gap-4">
                @if($admissionOpen)
                <a href="{{ route('admission.apply') }}" class="px-8 py-3 bg-white text-blue-700 font-semibold rounded-xl hover:bg-blue-50 transition shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i>Apply Now
                </a>
                @endif
                <a href="{{ route('frontend.programs') }}" class="px-8 py-3 border-2 border-white/50 text-white font-semibold rounded-xl hover:bg-white/10 transition">
                    <i class="fas fa-book-open mr-2"></i>Our Programs
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['students']) }}</div>
                <div class="text-sm text-gray-500 mt-1">Students</div>
            </div>
            <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-green-600">{{ $stats['programs'] }}</div>
                <div class="text-sm text-gray-500 mt-1">Programs</div>
            </div>
            <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-purple-600">{{ $stats['staff'] }}</div>
                <div class="text-sm text-gray-500 mt-1">Faculty & Staff</div>
            </div>
            <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-orange-600">{{ $stats['departments'] }}</div>
                <div class="text-sm text-gray-500 mt-1">Departments</div>
            </div>
        </div>
    </div>
</section>

{{-- Programs --}}
@if($programs->count())
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900">Our Programs</h2>
            <p class="text-gray-500 mt-2">Discover our range of academic programs</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($programs as $program)
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-book-open text-blue-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ $program->name }}</h3>
                <p class="text-sm text-gray-500 mb-3">{{ $program->code }} • {{ ucfirst($program->degree_type ?? 'Degree') }}</p>
                <div class="flex items-center gap-4 text-xs text-gray-400">
                    <span><i class="fas fa-clock mr-1"></i>{{ $program->duration_years ?? 4 }} Years</span>
                    <span><i class="fas fa-book mr-1"></i>{{ $program->total_credits ?? 120 }} Credits</span>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('frontend.programs') }}" class="text-blue-600 font-medium hover:underline">View All Programs →</a>
        </div>
    </div>
</section>
@endif

{{-- Notices --}}
@if($notices->count())
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Latest Notices</h2>
        <div class="grid md:grid-cols-2 gap-6">
            @foreach($notices as $notice)
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bullhorn text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $notice->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($notice->content), 150) }}</p>
                        <p class="text-xs text-gray-400 mt-2"><i class="fas fa-calendar mr-1"></i>{{ $notice->publish_date->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
@if($admissionOpen)
<section class="hero-gradient text-white py-16">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Start Your Journey?</h2>
        <p class="text-blue-100 mb-8">Applications are now open for the upcoming academic year. Apply today!</p>
        <a href="{{ route('admission.apply') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-white text-blue-700 font-semibold rounded-xl hover:bg-blue-50 transition shadow-lg">
            <i class="fas fa-paper-plane"></i> Apply for Admission
        </a>
    </div>
</section>
@endif

{{-- Footer --}}
<footer class="bg-gray-900 text-gray-400 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-white font-bold text-lg mb-4">{{ $institution->name }}</h3>
                <p class="text-sm">{{ $institution->address }}, {{ $institution->city }}, {{ $institution->country }}</p>
                <p class="text-sm mt-2"><i class="fas fa-envelope mr-2"></i>{{ $institution->email }}</p>
                <p class="text-sm"><i class="fas fa-phone mr-2"></i>{{ $institution->phone }}</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('frontend.about') }}" class="block hover:text-white">About Us</a>
                    <a href="{{ route('frontend.programs') }}" class="block hover:text-white">Programs</a>
                    <a href="{{ route('frontend.contact') }}" class="block hover:text-white">Contact</a>
                    <a href="{{ route('login') }}" class="block hover:text-white">Student Portal</a>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Connect</h4>
                <div class="flex gap-3">
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-sky-500 transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-pink-600 transition"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-xs">
            &copy; {{ date('Y') }} {{ $institution->name }}. Powered by <a href="https://github.com/PeeapDev" class="text-blue-400 hover:underline">OpenCollege</a>
        </div>
    </div>
</footer>
</body>
</html>
