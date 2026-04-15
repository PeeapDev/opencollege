<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply — {{ $institution->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
