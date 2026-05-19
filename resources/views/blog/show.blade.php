@extends('layouts.app')

@section('title', $post->title)
@section('meta_desc', $post->excerpt)
@section('canonical', route('blog.show', $post))

@push('head')
@php
$ldJson = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->title,
    'description' => $post->excerpt,
    'datePublished' => $post->published_at?->toIso8601String(),
    'dateModified' => $post->updated_at?->toIso8601String(),
    'author' => ['@type' => 'Organization', 'name' => 'APIForge'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
<script type="application/ld+json">{!! $ldJson !!}</script>
@endpush

@php
$relatedPosts = \App\Models\BlogPost::published()
    ->where('id', '!=', $post->id)
    ->latest('published_at')
    ->take(3)
    ->get();
@endphp

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <nav class="mb-6 text-sm text-gray-500">
        <a href="{{ url('/') }}" class="hover:text-white">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a>
        <span class="mx-2">/</span>
        <span class="text-white truncate">{{ $post->title }}</span>
    </nav>

    <div class="grid gap-10 lg:grid-cols-[1fr_320px]">
        <article>
            <header class="mb-8">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide
                        {{ $post->category === 'pricing' ? 'bg-yellow-500/10 text-yellow-400' : '' }}
                        {{ $post->category === 'news' ? 'bg-blue-500/10 text-blue-400' : '' }}
                        {{ $post->category === 'guide' ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                        {{ $post->category === 'analysis' ? 'bg-purple-500/10 text-purple-400' : '' }}">
                        {{ $post->category }}
                    </span>
                    <time class="text-sm text-gray-500" datetime="{{ $post->published_at->toDateString() }}">
                        {{ $post->published_at->format('F d, Y') }}
                    </time>
                    <span class="text-sm text-gray-600">&middot; {{ str_word_count(strip_tags($post->renderedContent())) }} words</span>
                </div>

                <h1 class="text-3xl font-extrabold text-white sm:text-4xl lg:text-5xl leading-tight tracking-tight">
                    {{ $post->title }}
                </h1>

                @if($post->excerpt)
                    <p class="mt-4 text-lg text-gray-400 leading-relaxed border-l-4 border-emerald-500 pl-4">
                        {{ $post->excerpt }}
                    </p>
                @endif

                <div class="mt-6 flex items-center gap-4 border-b border-gray-800 pb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/20">
                            <span class="text-emerald-400 font-bold text-sm">AF</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">APIForge Team</p>
                            <p class="text-xs text-gray-500">AI Pricing Research</p>
                        </div>
                    </div>
                    @if($post->source_name)
                        <div class="flex items-center gap-2 text-sm text-gray-500 border-l border-gray-800 pl-4">
                            <span>Via</span>
                            @if($post->source_url)
                                <a href="{{ $post->source_url }}" target="_blank" rel="nofollow noopener" class="text-emerald-400 hover:underline font-medium">{{ $post->source_name }}</a>
                            @else
                                <span class="text-gray-300">{{ $post->source_name }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </header>

            <div class="prose prose-lg prose-invert max-w-none
                prose-headings:text-white prose-headings:font-bold prose-headings:tracking-tight
                prose-h2:text-2xl prose-h2:mt-16 prose-h2:mb-6 prose-h2:pb-3 prose-h2:border-b prose-h2:border-gray-800
                prose-h3:text-xl prose-h3:mt-12 prose-h3:mb-5
                prose-h4:text-lg prose-h4:mt-10 prose-h4:mb-3 prose-h4:text-emerald-400
                prose-p:text-gray-300 prose-p:leading-relaxed prose-p:mb-6 prose-p:text-[1.05rem]
                prose-a:text-emerald-400 prose-a:no-underline hover:prose-a:underline prose-a:font-medium
                prose-li:text-gray-300 prose-li:my-2 prose-li:leading-relaxed
                prose-ul:my-6 prose-ol:my-6
                prose-strong:text-white prose-strong:font-semibold
                prose-blockquote:border-emerald-500 prose-blockquote:bg-gray-900/50 prose-blockquote:py-4 prose-blockquote:px-6 prose-blockquote:rounded-r-lg prose-blockquote:text-gray-300 prose-blockquote:not-italic prose-blockquote:my-8
                prose-code:text-emerald-300 prose-code:bg-gray-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-sm prose-code:before:content-none prose-code:after:content-none
                prose-pre:bg-gray-900 prose-pre:border prose-pre:border-gray-800 prose-pre:rounded-xl prose-pre:my-8
                prose-img:rounded-xl prose-img:border prose-img:border-gray-800 prose-img:my-8
                prose-hr:border-gray-800 prose-hr:my-12
                prose-table:border-separate prose-table:border-spacing-0 prose-table:w-full prose-table:rounded-xl prose-table:overflow-hidden prose-table:my-8
                prose-thead:border-none
                prose-th:border-b prose-th:border-gray-700 prose-th:bg-gray-900 prose-th:px-5 prose-th:py-3 prose-th:text-sm prose-th:font-semibold prose-th:text-white prose-th:text-left
                prose-td:border-b prose-td:border-gray-800 prose-td:px-5 prose-td:py-3 prose-td:text-sm prose-td:text-gray-300
                prose-tr:last-child:border-none">
                {!! $post->renderedContent() !!}
            </div>

            @production
            @if(env('CARBON_ADS_ID'))
            <div class="mt-8 rounded-xl border border-gray-800 bg-gray-900/20 p-4 text-center">
                <p class="text-[10px] text-gray-600 uppercase tracking-widest mb-2">Sponsored</p>
                <script async src="//cdn.carbonads.com/carbon.js?serve={{ env('CARBON_ADS_ID') }}&placement={{ parse_url(config('app.url'), PHP_URL_HOST) }}blog" id="_carbonads_js_blog"></script>
                <div class="min-h-[90px] flex items-center justify-center text-xs text-gray-600">Loading...</div>
            </div>
            @endif
            @endproduction

            <div class="mt-10 rounded-xl border border-emerald-500/20 bg-gradient-to-r from-emerald-500/5 to-gray-900 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">Find the best AI API for your budget</h3>
                        <p class="mt-1 text-sm text-gray-400">Compare 28 models across 10 providers with our live cost calculator.</p>
                    </div>
                    <a href="{{ url('/') }}" class="shrink-0 inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-6 py-3 font-semibold text-black hover:bg-emerald-400 transition text-sm">
                        Compare Models
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>

            @if($relatedPosts->isNotEmpty())
                <div class="mt-12">
                    <h3 class="text-lg font-bold text-white mb-5">Related Articles</h3>
                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach($relatedPosts as $related)
                            <a href="{{ route('blog.show', $related) }}" class="block rounded-xl border border-gray-800 bg-gray-900/50 p-4 hover:border-emerald-500/30 transition">
                                <span class="text-xs text-gray-500 uppercase">{{ $related->category }}</span>
                                <h4 class="mt-1 text-sm font-semibold text-white line-clamp-2">{{ $related->title }}</h4>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </article>

        <aside class="space-y-6">
            <div class="sticky top-20 space-y-6">
                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-5">
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wide mb-3">Key Takeaways</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li class="flex gap-2"><span class="text-emerald-400 shrink-0">&check;</span> Latest pricing data analyzed</li>
                        <li class="flex gap-2"><span class="text-emerald-400 shrink-0">&check;</span> Cost comparison across providers</li>
                        <li class="flex gap-2"><span class="text-emerald-400 shrink-0">&check;</span> Practical recommendations</li>
                    </ul>
                </div>

                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-5">
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wide mb-3">Explore by Category</h4>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('blog.index') }}?category=pricing" class="rounded-full bg-yellow-500/10 px-3 py-1 text-xs text-yellow-400 hover:bg-yellow-500/20 transition">Pricing</a>
                        <a href="{{ route('blog.index') }}?category=news" class="rounded-full bg-blue-500/10 px-3 py-1 text-xs text-blue-400 hover:bg-blue-500/20 transition">News</a>
                        <a href="{{ route('blog.index') }}?category=guide" class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs text-emerald-400 hover:bg-emerald-500/20 transition">Guides</a>
                        <a href="{{ route('blog.index') }}?category=analysis" class="rounded-full bg-purple-500/10 px-3 py-1 text-xs text-purple-400 hover:bg-purple-500/20 transition">Analysis</a>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-5">
                    @include('partials.email-capture')
                </div>

                <div class="rounded-xl border border-emerald-500/30 bg-gradient-to-br from-emerald-500/5 to-gray-900 p-5">
                    <h4 class="text-sm font-semibold text-white">Get 3 Free API Quotes</h4>
                    <p class="text-xs text-gray-400 mt-1 mb-3">Enterprise buyers: we match you with best-priced providers.</p>
                    <a href="#lead-form" class="block text-center rounded-lg bg-emerald-500 py-2.5 text-sm font-semibold text-black hover:bg-emerald-400 transition">
                        Get Quotes
                    </a>
                </div>
            </div>
        </aside>
    </div>

    @include('partials.lead-form')
</div>
@endsection
