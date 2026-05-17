@extends('layouts.app')

@section('title', 'AI API Pricing News & Updates — APIForge Blog')
@section('meta_desc', 'Daily AI industry news, API pricing changes, and model comparisons. Stay updated on OpenAI, Anthropic, Google, and more.')

@push('head')
@php
$ldJson = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Blog',
    'name' => 'APIForge Blog',
    'description' => 'Daily AI industry news, API pricing changes, and model comparisons.',
    'url' => route('blog.index'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
<script type="application/ld+json">{!! $ldJson !!}</script>
@endpush

@section('content')
<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold text-white sm:text-5xl tracking-tight">
            AI Pricing <span class="text-emerald-400">Blog</span>
        </h1>
        <p class="mt-4 text-lg text-gray-400 max-w-2xl mx-auto">
            Daily updates on AI API pricing changes, model launches, industry analysis, and cost optimization guides.
        </p>
    </div>

    <div class="mb-8 flex flex-wrap justify-center gap-2">
        <a href="{{ route('blog.index') }}" class="rounded-full px-4 py-2 text-sm font-medium transition {{ !$category ? 'bg-emerald-500 text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">All</a>
        <a href="{{ route('blog.index') }}?category=news" class="rounded-full px-4 py-2 text-sm font-medium transition {{ $category === 'news' ? 'bg-blue-500 text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">News</a>
        <a href="{{ route('blog.index') }}?category=pricing" class="rounded-full px-4 py-2 text-sm font-medium transition {{ $category === 'pricing' ? 'bg-yellow-500 text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">Pricing</a>
        <a href="{{ route('blog.index') }}?category=guide" class="rounded-full px-4 py-2 text-sm font-medium transition {{ $category === 'guide' ? 'bg-emerald-500 text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">Guides</a>
        <a href="{{ route('blog.index') }}?category=analysis" class="rounded-full px-4 py-2 text-sm font-medium transition {{ $category === 'analysis' ? 'bg-purple-500 text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">Analysis</a>
    </div>

    @if($posts->isEmpty())
        <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-16 text-center">
            <p class="text-lg text-gray-400">No posts yet. Check back soon for AI pricing news and updates.</p>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
                <a href="{{ route('blog.show', $post) }}" class="group flex flex-col rounded-xl border border-gray-800 bg-gray-900/50 hover:border-emerald-500/30 hover:bg-gray-900 transition overflow-hidden">
                    <div class="flex-1 p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase
                                {{ $post->category === 'pricing' ? 'bg-yellow-500/10 text-yellow-400' : '' }}
                                {{ $post->category === 'news' ? 'bg-blue-500/10 text-blue-400' : '' }}
                                {{ $post->category === 'guide' ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                                {{ $post->category === 'analysis' ? 'bg-purple-500/10 text-purple-400' : '' }}">
                                {{ $post->category }}
                            </span>
                            <span class="text-xs text-gray-600">{{ $post->published_at->format('M d, Y') }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition line-clamp-2 leading-snug">
                            {{ $post->title }}
                        </h3>
                        @if($post->excerpt)
                            <p class="mt-2 text-sm text-gray-400 line-clamp-3 leading-relaxed">{{ $post->excerpt }}</p>
                        @endif
                    </div>
                    <div class="px-5 py-3 border-t border-gray-800 flex items-center gap-2 text-xs text-gray-500">
                        <span>{{ str_word_count(strip_tags($post->renderedContent())) }} words</span>
                        <span>&middot;</span>
                        <span>{{ ceil(str_word_count(strip_tags($post->renderedContent())) / 200) }} min read</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @endif
</section>
@endsection
