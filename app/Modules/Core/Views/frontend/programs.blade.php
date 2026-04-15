<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs — {{ $institution->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-white">
@include('core::frontend._nav', ['institution' => $institution])
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900">Academic Programs</h1>
            <p class="text-gray-500 mt-2">Explore our range of degree programs</p>
        </div>
        @if($programs->count())
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($programs as $program)
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-book-open text-blue-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-lg text-gray-900 mb-1">{{ $program->name }}</h3>
                <p class="text-sm text-gray-500 mb-3">{{ $program->code }} • {{ ucfirst($program->degree_type ?? 'Degree') }}</p>
                @if($program->department)
                <p class="text-xs text-gray-400 mb-3">
                    <i class="fas fa-building mr-1"></i>{{ $program->department->name }}
                    @if($program->department->faculty) • {{ $program->department->faculty->name }}@endif
                </p>
                @endif
                <div class="flex items-center gap-4 text-xs text-gray-400">
                    <span><i class="fas fa-clock mr-1"></i>{{ $program->duration_years ?? 4 }} Years</span>
                    <span><i class="fas fa-book mr-1"></i>{{ $program->total_credits ?? 120 }} Credits</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-book-open text-4xl mb-3"></i>
            <p>No programs listed yet.</p>
        </div>
        @endif
    </div>
</section>
@include('core::frontend._footer', ['institution' => $institution])
</body>
</html>
