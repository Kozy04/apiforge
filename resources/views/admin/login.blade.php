<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — APIForge</title>
    @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    <link rel="stylesheet" href="/build/{{ $manifest['resources/css/app.css']['file'] }}">
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-950">
    <div class="w-full max-w-sm rounded-2xl border border-gray-800 bg-gray-900 p-8 shadow-2xl">
        <h1 class="text-2xl font-bold text-white text-center mb-1">API<span class="text-emerald-400">Forge</span></h1>
        <p class="text-sm text-gray-500 text-center mb-8">Admin Panel</p>

        @if($errors->any())
            <div class="mb-6 rounded-lg bg-red-500/10 border border-red-500/30 px-4 py-3 text-sm text-red-400">
                Invalid password. Please try again.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
            <input type="password" name="password" placeholder="Enter admin password"
                   class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition mb-5"
                   autofocus>
            <button type="submit"
                    class="w-full rounded-lg bg-emerald-500 py-3 font-semibold text-black hover:bg-emerald-400 transition cursor-pointer">
                Sign In
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-600">
            <a href="{{ url('/') }}" class="hover:text-gray-400 transition">&larr; Back to site</a>
        </p>
    </div>
</body>
</html>
