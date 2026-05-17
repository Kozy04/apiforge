@extends('layouts.app')

@php
    $title = "{$modelOne->name} vs {$modelTwo->name}: Pricing Comparison 2025";
    $desc = "Compare {$modelOne->name} (\$".number_format($modelOne->input_cost_per_m,2)."/M input) vs {$modelTwo->name} (\$".number_format($modelTwo->input_cost_per_m,2)."/M input). Side-by-side pricing, specs, and cost calculator.";
@endphp

@section('title', $title)
@section('meta_desc', $desc)

@push('head')
    @include('partials.schema-compare')
@endpush

@section('content')
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
    <nav class="mb-8 text-sm text-gray-500">
        <a href="{{ url('/') }}" class="hover:text-white">All Models</a>
        <span class="mx-2">/</span>
        <span class="text-white">Compare</span>
    </nav>

    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold text-white sm:text-4xl">
            {{ $modelOne->name }} <span class="text-gray-500">vs</span> {{ $modelTwo->name }}
        </h1>
        <p class="mt-2 text-gray-400">Side-by-side pricing and specifications comparison.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        @foreach([['m' => $modelOne, 'label' => 'A'], ['m' => $modelTwo, 'label' => 'B']] as $item)
        @php $m = $item['m']; @endphp
        <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6 {{ $m->id === $cheaperInput->id ? 'ring-1 ring-emerald-500/50' : '' }}">
            @if($m->id === $cheaperInput->id)
                <span class="inline-block rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-400 mb-3">
                    Cheaper Input
                </span>
            @endif
            <h2 class="text-xl font-bold text-white">{{ $m->name }}</h2>
            <p class="text-sm text-gray-400">{{ $m->provider->name }}</p>

            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-400">Input</dt>
                    <dd class="font-mono text-white font-bold">
                        ${{ number_format($m->input_cost_per_m, 6) }}/M
                        @if($m->input_cost_per_m > $cheaperInput->input_cost_per_m)
                            <span class="text-red-400 text-xs">
                                +{{ number_format((($m->input_cost_per_m / $cheaperInput->input_cost_per_m) - 1) * 100, 0) }}%
                            </span>
                        @elseif($m->input_cost_per_m === $cheaperInput->input_cost_per_m && $m->id !== $cheaperInput->id)
                            <span class="text-gray-500 text-xs">=</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Output</dt>
                    <dd class="font-mono text-white font-bold">
                        ${{ number_format($m->output_cost_per_m, 6) }}/M
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Context</dt>
                    <dd class="font-mono text-white">{{ number_format($m->context_window) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Latency</dt>
                    <dd class="font-mono text-white">{{ $m->latency_score }}s</dd>
                </div>
            </dl>
        </div>
        @endforeach
    </div>

    @include('partials.cost-calculator', ['single' => false, 'modelOne' => $modelOne, 'modelTwo' => $modelTwo])

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        @include('partials.lead-form')
        @include('partials.email-capture')
    </div>

    <div class="mt-10 text-center">
        <h3 class="text-lg font-semibold text-white">Try the cheaper option</h3>
        <p class="text-gray-400 mt-1">Get started with {{ $cheaperInput->name }} on {{ $cheaperInput->provider->name }}.</p>
        <a href="{{ $cheaperInput->provider->trackedUrl($cheaperInput->slug, request()->path()) }}" target="_blank" rel="nofollow noopener"
           class="mt-4 inline-block rounded-lg bg-emerald-500 px-6 py-3 font-semibold text-black hover:bg-emerald-400 transition">
            Get API Key on {{ $cheaperInput->provider->name }} &rarr;
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initDualCalculator(
            '{{ $modelOne->input_cost_per_m }}', '{{ $modelOne->output_cost_per_m }}',
            '{{ $modelTwo->input_cost_per_m }}', '{{ $modelTwo->output_cost_per_m }}'
        );
    });
</script>
@endpush
