@extends('layouts.app')

@section('title', 'AI Model API Pricing — Live Cost Calculator')
@section('meta_desc', 'Browse and compare pricing for every major AI model API: OpenAI, Anthropic, Google Gemini, Groq, Mistral, DeepSeek, and more.')

@push('head')
    @include('partials.schema-index')
@endpush

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">
            AI Model API <span class="text-emerald-400">Pricing</span>
        </h1>
        <p class="mt-4 text-lg text-gray-400 max-w-2xl mx-auto">
            Compare costs per million tokens across all major providers. Use the live calculator to estimate your monthly spend.
        </p>
    </div>

    <x-search />

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($models as $model)
            <a href="{{ route('model.show', $model) }}"
               class="group block rounded-xl border border-gray-800 bg-gray-900/50 p-5 hover:border-emerald-500/50 hover:bg-gray-900 transition">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold text-white group-hover:text-emerald-400 transition">
                            {{ $model->name }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-400">{{ $model->provider->name }}</p>
                    </div>
                    <span class="rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-400">
                        {{ $model->context_window ? number_format($model->context_window / 1000) . 'K ctx' : 'N/A' }}
                    </span>
                </div>
                <div class="mt-4 flex gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Input</span>
                        <p class="font-mono text-white">${{ number_format($model->input_cost_per_m, 2) }}/M</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Output</span>
                        <p class="font-mono text-white">${{ number_format($model->output_cost_per_m, 2) }}/M</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endsection
