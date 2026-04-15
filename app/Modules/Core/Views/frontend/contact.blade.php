<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact — {{ $institution->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-white">
@include('core::frontend._nav', ['institution' => $institution])
<section class="py-16">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900">Contact Us</h1>
            <p class="text-gray-500 mt-2">Get in touch with {{ $institution->name }}</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6 mb-12">
            <div class="bg-blue-50 rounded-xl p-6 text-center">
                <i class="fas fa-map-marker-alt text-blue-600 text-2xl mb-3"></i>
                <h3 class="font-semibold text-gray-900 mb-1">Address</h3>
                <p class="text-sm text-gray-600">{{ $institution->address ?? 'Address not set' }}</p>
                <p class="text-sm text-gray-600">{{ $institution->city ?? '' }}, {{ $institution->country ?? 'Sierra Leone' }}</p>
            </div>
            <div class="bg-green-50 rounded-xl p-6 text-center">
                <i class="fas fa-envelope text-green-600 text-2xl mb-3"></i>
                <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                <p class="text-sm text-gray-600">{{ $institution->email ?? $settings->contact_email ?? 'Not available' }}</p>
            </div>
            <div class="bg-purple-50 rounded-xl p-6 text-center">
                <i class="fas fa-phone text-purple-600 text-2xl mb-3"></i>
                <h3 class="font-semibold text-gray-900 mb-1">Phone</h3>
                <p class="text-sm text-gray-600">{{ $institution->phone ?? $settings->contact_phone ?? 'Not available' }}</p>
            </div>
        </div>
    </div>
</section>
@include('core::frontend._footer', ['institution' => $institution])
</body>
</html>
