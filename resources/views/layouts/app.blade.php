<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'APIForge — AI Model Pricing & Cost Calculator')</title>
    <meta name="description" content="@yield('meta_desc', 'Compare AI model API pricing across OpenAI, Anthropic, Google, and more. Live cost calculator included.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
    $schemaFile = public_path('schema-index.json');
    @endphp
    @if(file_exists($schemaFile))
    <script type="application/ld+json">{!! file_get_contents($schemaFile) !!}</script>
    @endif
    @stack('head')
</head>
<body class="bg-gray-950 text-gray-100 antialiased">
    <div class="flex min-h-screen flex-col">
        <header class="border-b border-gray-800 bg-gray-900/50 backdrop-blur-sm sticky top-0 z-50">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6">
                <a href="{{ url('/') }}" class="text-xl font-bold text-emerald-400 tracking-tight">
                    API<span class="text-white">Forge</span>
                </a>
                <div class="hidden md:flex items-center gap-6 text-sm text-gray-300">
                    <a href="{{ url('/') }}" class="hover:text-white transition">All Models</a>
                    <a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a>
                </div>
                <div class="md:hidden">
                    <span class="text-gray-400 text-sm">Menu</span>
                </div>
            </nav>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="border-t border-gray-800 bg-gray-900 py-8 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} APIForge. AI model pricing data updated weekly.</p>
        </footer>
    </div>

    @include('partials.ads')

    @stack('scripts')
</body>
</html>
