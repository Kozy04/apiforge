<!DOCTYPE html>
<html lang="en" class="bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — APIForge</title>
    @vite(['resources/css/app.css'])
    <style>body { background: #0a0a0f; }</style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-950">
    <div class="w-full max-w-sm rounded-2xl border border-gray-800 bg-gray-900 p-8">
        <h1 class="text-2xl font-bold text-white text-center mb-2">API<span class="text-emerald-400">Forge</span></h1>
        <p class="text-sm text-gray-400 text-center mb-6">Admin Panel</p>

        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-500/10 border border-red-500/30 px-4 py-2 text-sm text-red-400">
                Invalid password.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <input type="password" name="password" placeholder="Admin password"
                   class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none mb-4"
                   autofocus>
            <button type="submit"
                    class="w-full rounded-lg bg-emerald-500 py-3 font-semibold text-black hover:bg-emerald-400 transition">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>
