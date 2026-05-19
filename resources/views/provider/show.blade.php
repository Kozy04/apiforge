@extends('layouts.app')

@section('title', "{$provider->name} API Models — Pricing & Comparison")
@section('meta_desc', "Browse all {$provider->name} AI models and API pricing. Compare costs per million tokens, context windows, and latency scores.")

@section('content')
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
    <nav class="mb-8 text-sm text-gray-500">
        <a href="{{ url('/') }}" class="hover:text-white">All Models</a>
        <span class="mx-2">/</span>
        <a href="{{ route('provider.index') }}" class="hover:text-white">Providers</a>
        <span class="mx-2">/</span>
        <span class="text-white">{{ $provider->name }}</span>
    </nav>

    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-white sm:text-4xl tracking-tight">{{ $provider->name }} <span class="text-emerald-400">Models</span></h1>
        <p class="mt-3 text-gray-400">{{ $provider->apiModels->count() }} models available &middot;
            <a href="{{ $provider->trackedUrl(null, request()->path()) }}" target="_blank" rel="nofollow noopener" class="text-emerald-400 hover:underline">Visit {{ $provider->name }} &rarr;</a>
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($provider->apiModels as $model)
            <a href="{{ route('model.show', $model) }}" class="group block rounded-xl border border-gray-800 bg-gray-900/50 p-5 hover:border-emerald-500/50 hover:bg-gray-900 transition">
                <h3 class="font-semibold text-white group-hover:text-emerald-400 transition">{{ $model->name }}</h3>
                <div class="mt-3 flex gap-4 text-sm">
                    <div><span class="text-gray-500">Input</span><p class="font-mono text-white">${{ number_format($model->input_cost_per_m, 2) }}/M</p></div>
                    <div><span class="text-gray-500">Output</span><p class="font-mono text-white">${{ number_format($model->output_cost_per_m, 2) }}/M</p></div>
                </div>
                <div class="mt-3 flex items-center gap-3 text-xs text-gray-500">
                    <span>{{ number_format($model->context_window) }} ctx</span>
                    <span>&middot;</span>
                    <span>{{ $model->latency_score }}s</span>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endsection
