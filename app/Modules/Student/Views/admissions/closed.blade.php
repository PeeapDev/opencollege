<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admissions Closed — {{ $institution->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
<div class="text-center max-w-md px-4">
    <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-door-closed text-amber-600 text-3xl"></i>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Admissions Currently Closed</h1>
    <p class="text-gray-500 mb-6">{{ $institution->name }} is not accepting applications at this time. Please check back later.</p>
    <a href="{{ route('frontend.home') }}" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Back to Homepage</a>
</div>
</body>
</html>
