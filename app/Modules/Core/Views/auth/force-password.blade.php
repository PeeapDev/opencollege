<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password — OpenCollege</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center p-6">
<div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
    <h1 class="text-2xl font-bold text-slate-900 mb-2">Set a new password</h1>
    <p class="text-slate-600 text-sm mb-6">
        Your account is using a temporary password. Please set a new one to continue.
    </p>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg p-3 mb-4">
            @foreach ($errors->all() as $err)<div>{{ $err }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="/password/force-update" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Current (temporary) password</label>
            <input type="password" name="current_password" required autofocus
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">New password</label>
            <input type="password" name="password" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-slate-500 mt-1">Min 8 characters, must contain letters and numbers.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Confirm new password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
            Update Password
        </button>
    </form>

    <form method="POST" action="/logout" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm text-slate-500 hover:text-slate-700">Log out instead</button>
    </form>
</div>
</body>
</html>
