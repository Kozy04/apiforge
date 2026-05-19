@extends('layouts.app')

@section('title', $activeCategory ? ucfirst($activeCategory) . ' AI Models — Pricing & Comparison' : 'AI Model API Pricing — Live Cost Calculator')
@section('meta_desc', $activeCategory ? "Compare " . ucfirst($activeCategory) ." AI model API pricing. Live cost calculator, side-by-side comparisons." : 'Compare 28+ AI model APIs across 16 providers. Live pricing, cost calculator, side-by-side comparisons.')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
            @if($activeCategory)
                {{ match($activeCategory) {
                    'text' => 'Text Generation',
                    'image' => 'Image Generation',
                    'video' => 'Video Generation',
                    'audio' => 'Speech & Audio',
                    'embedding' => 'Embeddings',
                    'open-source' => 'Open Source',
                    default => ucfirst($activeCategory),
                } }}
            @else
                AI Model API
            @endif
            <span class="text-emerald-400">Pricing</span>
        </h1>
        <p class="mt-4 text-lg text-gray-400 max-w-2xl mx-auto">
            @if($activeCategory)
                Browse {{ $models->count() }} {{ match($activeCategory) {'text' => 'text', 'image' => 'image', 'video' => 'video', 'audio' => 'speech/audio', 'embedding' => 'embedding', 'open-source' => 'open-source', default => ''} }} generation models.
            @else
                Compare costs across {{ $models->count() }} models and 16 providers. Use the live calculator to estimate your monthly spend.
            @endif
        </p>
    </div>

    <x-search />

    <div class="mt-6 flex flex-wrap justify-center gap-2">
        <a href="{{ url('/') }}" class="rounded-full px-4 py-2 text-sm font-medium transition {{ !$activeCategory ? 'bg-emerald-500 text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
            All ({{ App\Models\ApiModel::count() }})
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('category.show', $cat) }}" class="rounded-full px-4 py-2 text-sm font-medium transition flex items-center gap-1.5 {{ $activeCategory === $cat ? 'bg-emerald-500 text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                {{ match($cat) {'text' => 'Text', 'image' => 'Image', 'video' => 'Video', 'audio' => 'Audio', 'embedding' => 'Embeddings', 'open-source' => 'Open Source', default => ucfirst($cat)} }}
                <span class="text-xs opacity-60">({{ App\Models\ApiModel::where('category', $cat)->count() }})</span>
            </a>
        @endforeach
    </div>

    @php
    $sponsored = collect(explode(',', env('SPONSORED_PROVIDERS', '')))
        ->filter()
        ->map(fn($s) => App\Models\Provider::where('slug', trim($s))->first())
        ->filter();
    @endphp
    @if($sponsored->isNotEmpty())
    <div class="mt-10">
        <div class="flex items-center gap-2 mb-5">
            <h2 class="text-xl font-bold text-white">Featured Providers</h2>
            <span class="rounded-full bg-amber-500/10 px-2.5 py-0.5 text-[10px] font-medium text-amber-400 uppercase tracking-wide">Sponsored</span>
        </div>
        <div class="grid gap-4 sm:grid-cols-3">
            @foreach($sponsored as $sp)
                <a href="{{ route('provider.show', $sp) }}" class="group block rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-500/5 to-gray-900 p-5 hover:border-amber-500/50 transition ring-1 ring-amber-500/10">
                    <h3 class="font-semibold text-white group-hover:text-amber-400 transition">{{ $sp->name }}</h3>
                    <p class="mt-1 text-xs text-gray-400">{{ $sp->apiModels->count() }} models</p>
                    <p class="mt-3 text-xs text-gray-500 line-clamp-2">{{ $sp->affiliate_url }}</p>
                </a>
            @endforeach
        </div>
    </div>
    @endif

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
