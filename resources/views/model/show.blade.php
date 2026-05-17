@extends('layouts.app')

@section('title', "{$model->name} Pricing & Cost Calculator (2025)")
@section('meta_desc', "See {$model->name} API costs: \$".number_format($model->input_cost_per_m, 2)."/M input tokens, \$".number_format($model->output_cost_per_m, 2)."/M output tokens. {$model->context_window} context window. Use our live cost calculator.")
@section('canonical', route('model.show', $model))

@push('head')
    @include('partials.schema-model')
@endpush

@section('content')
<section class="mx-auto max-w-4xl px-4 py-12 sm:px-6">
    <nav class="mb-8 text-sm text-gray-500">
        <a href="{{ url('/') }}" class="hover:text-white">All Models</a>
        <span class="mx-2">/</span>
        <span class="text-gray-300">{{ $model->provider->name }}</span>
        <span class="mx-2">/</span>
        <span class="text-white">{{ $model->name }}</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white sm:text-4xl">
            {{ $model->name }} <span class="text-emerald-400">Pricing</span>
        </h1>
        <p class="mt-2 text-gray-400">
            Provided by <a href="{{ $model->provider->trackedUrl($model->slug, request()->path()) }}" target="_blank" rel="nofollow noopener" class="text-emerald-400 hover:underline">{{ $model->provider->name }}</a>
        </p>
        <p class="mt-1 text-sm text-gray-500">
            Last updated: {{ $model->last_updated ? $model->last_updated->format('M d, Y') : 'N/A' }}
        </p>
    </div>

    <div class="grid gap-6 md:grid-cols-2 mb-8">
        <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Pricing</h2>
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-gray-400">Input cost (per 1M tokens)</dt>
                    <dd class="font-mono text-white font-bold">${{ number_format($model->input_cost_per_m, 6) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Output cost (per 1M tokens)</dt>
                    <dd class="font-mono text-white font-bold">${{ number_format($model->output_cost_per_m, 6) }}</dd>
                </div>
                <hr class="border-gray-800">
                <div class="flex justify-between">
                    <dt class="text-gray-400">Cost ratio (out/in)</dt>
                    <dd class="font-mono text-white">{{ $model->input_cost_per_m > 0 ? number_format($model->output_cost_per_m / $model->input_cost_per_m, 1) . 'x' : 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Specifications</h2>
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-gray-400">Context window</dt>
                    <dd class="font-mono text-white">{{ number_format($model->context_window) }} tokens</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Latency score</dt>
                    <dd class="font-mono text-white">{{ $model->latency_score }}s avg</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Provider</dt>
                    <dd class="text-white">{{ $model->provider->name }}</dd>
                </div>
            </dl>
        </div>
    </div>

    @include('partials.cost-calculator', ['single' => true])

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @include('partials.lead-form')
        </div>
        <div>
            @include('partials.email-capture')
        </div>
    </div>

    <div class="mt-10 rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-6 text-center">
        <h3 class="text-lg font-semibold text-white">Ready to use {{ $model->name }}?</h3>
        <p class="mt-1 text-gray-400">Sign up through {{ $model->provider->name }} and start building today.</p>
        <a href="{{ $model->provider->trackedUrl($model->slug, request()->path()) }}" target="_blank" rel="nofollow noopener"
           class="mt-4 inline-block rounded-lg bg-emerald-500 px-6 py-3 font-semibold text-black hover:bg-emerald-400 transition">
            Get API Key on {{ $model->provider->name }} &rarr;
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initCalculator('{{ $model->input_cost_per_m }}', '{{ $model->output_cost_per_m }}', 'calc-single');
    });
</script>
@endpush
