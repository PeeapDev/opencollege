<footer class="bg-gray-900 text-gray-400 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-white font-bold text-lg mb-4">{{ $institution->name }}</h3>
                <p class="text-sm">{{ $institution->address ?? '' }}{{ $institution->city ? ', '.$institution->city : '' }}</p>
                @if($institution->email)<p class="text-sm mt-2"><i class="fas fa-envelope mr-2"></i>{{ $institution->email }}</p>@endif
                @if($institution->phone)<p class="text-sm"><i class="fas fa-phone mr-2"></i>{{ $institution->phone }}</p>@endif
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
