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
    @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    <link rel="stylesheet" href="/build/{{ $manifest['resources/css/app.css']['file'] }}">
    <script type="module" src="/build/{{ $manifest['resources/js/app.js']['file'] }}" defer></script>
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
                    <a href="{{ route('provider.index') }}" class="hover:text-white transition">Providers</a>
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

        <footer class="border-t border-gray-800 bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
                <div class="grid gap-8 sm:grid-cols-3">
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wide">APIForge</h4>
                        <ul class="mt-3 space-y-2 text-sm text-gray-400">
                            <li><a href="{{ url('/') }}" class="hover:text-white transition">All Models</a></li>
                            <li><a href="{{ route('provider.index') }}" class="hover:text-white transition">Providers</a></li>
                            <li><a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a></li>
                            <li><a href="{{ route('advertise') }}" class="hover:text-white transition">Advertise</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wide">Top Models</h4>
                        <ul class="mt-3 space-y-2 text-sm text-gray-400">
                            <li><a href="{{ route('model.show', 'gpt-4o') }}" class="hover:text-white transition">GPT-4o</a></li>
                            <li><a href="{{ route('model.show', 'claude-3-5-sonnet') }}" class="hover:text-white transition">Claude 3.5 Sonnet</a></li>
                            <li><a href="{{ route('model.show', 'gemini-2-5-pro') }}" class="hover:text-white transition">Gemini 2.5 Pro</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-white uppercase tracking-wide">Popular Comparisons</h4>
                        <ul class="mt-3 space-y-2 text-sm text-gray-400">
                            <li><a href="{{ route('compare.show', ['model_one' => 'gpt-4o', 'model_two' => 'claude-3-5-sonnet']) }}" class="hover:text-white transition">GPT-4o vs Claude</a></li>
                            <li><a href="{{ route('compare.show', ['model_one' => 'gemini-2-5-pro', 'model_two' => 'gpt-4o']) }}" class="hover:text-white transition">Gemini vs GPT-4o</a></li>
                            <li><a href="{{ route('compare.show', ['model_one' => 'gpt-4o', 'model_two' => 'gpt-4o-mini']) }}" class="hover:text-white transition">GPT-4o vs GPT-4o mini</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-gray-800 text-center text-xs text-gray-600">
                    <p>&copy; {{ date('Y') }} APIForge. AI model pricing data updated weekly. <a href="{{ url('/sitemap.xml') }}" class="hover:text-gray-400">Sitemap</a></p>
                </div>
            </div>
        </footer>
    </div>

    @include('partials.ads')

    @stack('scripts')
</body>
</html>
