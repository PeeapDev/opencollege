<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-white"></i>
                </div>
                <span class="font-bold text-lg text-gray-900">{{ $institution->name }}</span>
            </div>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="{{ route('frontend.home') }}" class="{{ request()->routeIs('frontend.home') ? 'text-blue-600' : 'hover:text-blue-600' }}">Home</a>
                <a href="{{ route('frontend.about') }}" class="{{ request()->routeIs('frontend.about') ? 'text-blue-600' : 'hover:text-blue-600' }}">About</a>
                <a href="{{ route('frontend.programs') }}" class="{{ request()->routeIs('frontend.programs') ? 'text-blue-600' : 'hover:text-blue-600' }}">Programs</a>
                <a href="{{ route('frontend.contact') }}" class="{{ request()->routeIs('frontend.contact') ? 'text-blue-600' : 'hover:text-blue-600' }}">Contact</a>
                <a href="{{ route('login') }}" class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition">Login</a>
            </div>
        </div>
    </div>
</nav>
