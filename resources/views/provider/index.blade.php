@extends('layouts.app')

@section('title', 'AI Model API Providers — Compare All Platforms')
@section('meta_desc', 'Browse all AI API providers: OpenAI, Anthropic, Google, Groq, Mistral, DeepSeek, and more. Compare models and pricing.')

@push('head')
@php
$items = [];
foreach ($providers as $i => $p) {
    $items[] = [
        '@type' => 'ListItem', 'position' => $i + 1,
        'item' => ['@type' => 'Organization', 'name' => $p->name, 'url' => route('provider.show', $p)],
    ];
}
$ld = json_encode(['@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => 'AI API Providers', 'numberOfItems' => $providers->count(), 'itemListElement' => $items], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
@endphp
<script type="application/ld+json">{!! $ld !!}</script>
@endpush

@section('content')
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold text-white sm:text-5xl tracking-tight">API <span class="text-emerald-400">Providers</span></h1>
        <p class="mt-4 text-lg text-gray-400 max-w-2xl mx-auto">Browse all AI model API providers. Compare pricing, models, and features across every platform.</p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($providers as $provider)
            <a href="{{ route('provider.show', $provider) }}" class="group block rounded-xl border border-gray-800 bg-gray-900/50 p-6 hover:border-emerald-500/30 hover:bg-gray-900 transition">
                <h3 class="text-xl font-bold text-white group-hover:text-emerald-400 transition">{{ $provider->name }}</h3>
                <div class="mt-3 flex items-center gap-4 text-sm text-gray-400">
                    <span>{{ $provider->api_models_count }} models</span>
                    <span>&middot;</span>
                    <span>{{ $provider->clicks_count }} clicks</span>
                </div>
                <p class="mt-4 text-xs text-gray-500 font-mono truncate">{{ $provider->affiliate_url }}</p>
            </a>
        @endforeach
    </div>
</section>
@endsection
