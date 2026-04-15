<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About — {{ $institution->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-white">
@include('core::frontend._nav', ['institution' => $institution])
<section class="py-16">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">About {{ $institution->name }}</h1>
        <div class="prose max-w-none text-gray-600 leading-relaxed">
            @if($settings && $settings->about_text)
                {!! nl2br(e($settings->about_text)) !!}
            @else
                <p>{{ $institution->name }} is a leading institution of higher education committed to academic excellence, research, and community engagement.</p>
                <p class="mt-4">Located in {{ $institution->city ?? 'Sierra Leone' }}, we offer a range of undergraduate and postgraduate programs designed to prepare students for successful careers.</p>
            @endif
        </div>
        <div class="mt-8 grid md:grid-cols-3 gap-6">
            <div class="bg-blue-50 rounded-xl p-6 text-center">
                <i class="fas fa-bullseye text-blue-600 text-2xl mb-3"></i>
                <h3 class="font-semibold text-gray-900 mb-2">Our Mission</h3>
                <p class="text-sm text-gray-600">To provide quality education and foster innovation for the development of society.</p>
            </div>
            <div class="bg-green-50 rounded-xl p-6 text-center">
                <i class="fas fa-eye text-green-600 text-2xl mb-3"></i>
                <h3 class="font-semibold text-gray-900 mb-2">Our Vision</h3>
                <p class="text-sm text-gray-600">To be a world-class institution producing leaders and change-makers.</p>
            </div>
            <div class="bg-purple-50 rounded-xl p-6 text-center">
                <i class="fas fa-heart text-purple-600 text-2xl mb-3"></i>
                <h3 class="font-semibold text-gray-900 mb-2">Our Values</h3>
                <p class="text-sm text-gray-600">Integrity, excellence, inclusivity, innovation, and service to community.</p>
            </div>
        </div>
    </div>
</section>
@include('core::frontend._footer', ['institution' => $institution])
</body>
</html>
