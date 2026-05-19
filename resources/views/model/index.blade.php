@extends('layouts.app')

@section('title', 'AI Model API Pricing — Live Cost Calculator')
@section('meta_desc', 'Compare 28 AI model APIs across 10 providers. Live pricing, cost calculator, side-by-side comparisons. Find the cheapest LLM for your budget.')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
            AI Model API <span class="text-emerald-400">Pricing</span>
        </h1>
        <p class="mt-4 text-lg text-gray-400 max-w-2xl mx-auto">
            Compare costs per million tokens across 28 models and 10 providers. Use the live calculator to estimate your monthly spend.
        </p>
    </div>

    <x-search />

    @php
    $cheapest = $models->sortBy('input_cost_per_m')->take(3);
    $recent = $models->filter->isRecentlyUpdated()->take(3);
    $fastest = $models->sortBy('latency_score')->take(3);
    @endphp

    @if($cheapest->isNotEmpty())
    <div class="mt-12">
        <h2 class="text-xl font-bold text-white mb-5 flex items-center gap-2">
            <span class="text-yellow-400">&#9733;</span> Cheapest Models
        </h2>
        <div class="grid gap-4 sm:grid-cols-3">
            @foreach($cheapest as $model)
                @include('partials.model-card', ['model' => $model, 'highlight' => 'Cheapest Input'])
            @endforeach
        </div>
    </div>
    @endif

    @if($recent->isNotEmpty())
    <div class="mt-12">
        <h2 class="text-xl font-bold text-white mb-5 flex items-center gap-2">
            <span class="text-blue-400">&#9733;</span> Recently Updated
        </h2>
        <div class="grid gap-4 sm:grid-cols-3">
            @foreach($recent as $model)
                @include('partials.model-card', ['model' => $model, 'highlight' => 'Updated'])
            @endforeach
        </div>
    </div>
    @endif

    @if($fastest->isNotEmpty())
    <div class="mt-12">
        <h2 class="text-xl font-bold text-white mb-5 flex items-center gap-2">
            <span class="text-purple-400">&#9733;</span> Fastest Models
        </h2>
        <div class="grid gap-4 sm:grid-cols-3">
            @foreach($fastest as $model)
                @include('partials.model-card', ['model' => $model, 'highlight' => 'Fastest'])
            @endforeach
        </div>
    </div>
    @endif

    <div class="mt-14">
        <h2 class="text-xl font-bold text-white mb-5">All {{ $models->count() }} Models</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($models as $model)
                @include('partials.model-card', ['model' => $model])
            @endforeach
        </div>
    </div>

    <div class="mt-12 rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-6 text-center">
        <h3 class="text-lg font-semibold text-white">Not sure which model to choose?</h3>
        <p class="mt-1 text-gray-400">Use our head-to-head comparison tool to see detailed pricing and specs side by side.</p>
        <div class="mt-4 flex flex-wrap justify-center gap-2">
            <a href="{{ route('compare.show', ['model_one' => 'gpt-4o', 'model_two' => 'claude-3-5-sonnet']) }}" class="rounded-lg bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700 transition">
                GPT-4o vs Claude
            </a>
            <a href="{{ route('compare.show', ['model_one' => 'gemini-2-5-pro', 'model_two' => 'gpt-4o']) }}" class="rounded-lg bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700 transition">
                Gemini vs GPT-4o
            </a>
            <a href="{{ route('compare.show', ['model_one' => 'gpt-4o', 'model_two' => 'gpt-4o-mini']) }}" class="rounded-lg bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700 transition">
                GPT-4o vs Mini
            </a>
        </div>
    </div>
</section>
@endsection
