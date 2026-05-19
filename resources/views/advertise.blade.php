@extends('layouts.app')

@section('title', 'Advertise on APIForge — Reach AI Developers & Decision Makers')
@section('meta_desc', 'Promote your AI API, tool, or service to thousands of developers comparing AI model pricing. Sponsored listings, display ads, and newsletter placements available.')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-16 sm:px-6">
    <div class="mb-12 text-center">
        <h1 class="text-4xl font-extrabold text-white sm:text-5xl tracking-tight">
            Advertise on <span class="text-emerald-400">APIForge</span>
        </h1>
        <p class="mt-4 text-lg text-gray-400 max-w-2xl mx-auto">
            Reach developers and businesses actively comparing AI model APIs — the highest-intent audience in tech.
        </p>
    </div>

    <div class="grid gap-6 md:grid-cols-3 mb-16">
        <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6 text-center">
            <div class="text-3xl mb-3">&#9733;</div>
            <h3 class="text-lg font-bold text-white mb-2">Sponsored Provider Slot</h3>
            <p class="text-sm text-gray-400 mb-4">Your company featured at the top of the homepage with a "Sponsored" badge. First thing every visitor sees.</p>
            <p class="text-2xl font-bold text-amber-400">$200<span class="text-sm text-gray-500">/month</span></p>
        </div>

        <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6 text-center">
            <div class="text-3xl mb-3">&#9632;</div>
            <h3 class="text-lg font-bold text-white mb-2">Display Ads</h3>
            <p class="text-sm text-gray-400 mb-4">Carbon Ads placements on every page — seen by developers comparing models, reading blog posts, and using the calculator.</p>
            <p class="text-2xl font-bold text-amber-400">$10-25<span class="text-sm text-gray-500"> CPM</span></p>
        </div>

        <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6 text-center">
            <div class="text-3xl mb-3">&#9993;</div>
            <h3 class="text-lg font-bold text-white mb-2">Newsletter Sponsorship</h3>
            <p class="text-sm text-gray-400 mb-4">Your message sent to our subscriber list of AI developers and decision makers.</p>
            <p class="text-2xl font-bold text-amber-400">$150<span class="text-sm text-gray-500">/send</span></p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-8 mb-12">
        <h2 class="text-2xl font-bold text-white mb-4">Why Advertise Here?</h2>
        <div class="grid gap-4 sm:grid-cols-2 text-sm text-gray-400">
            <div class="flex gap-3">
                <span class="text-emerald-400 text-lg shrink-0">&check;</span>
                <span>Audience actively comparing AI model pricing — high commercial intent</span>
            </div>
            <div class="flex gap-3">
                <span class="text-emerald-400 text-lg shrink-0">&check;</span>
                <span>{% raw %}{{ App\Http\Controllers\ModelController@index }}{% endraw %}</span>
            </div>
            <div class="flex gap-3">
                <span class="text-emerald-400 text-lg shrink-0">&check;</span>
                <span>400+ indexed pages in Google with growing organic traffic</span>
            </div>
            <div class="flex gap-3">
                <span class="text-emerald-400 text-lg shrink-0">&check;</span>
                <span>Daily blog content keeps the site fresh and ranking</span>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-emerald-500/30 bg-gradient-to-br from-emerald-500/5 to-gray-900 p-8 text-center">
        <h2 class="text-2xl font-bold text-white mb-2">Ready to Reach AI Developers?</h2>
        <p class="text-gray-400 mb-6">Email us to discuss placement options, pricing, and availability.</p>
        <a href="mailto:advertise@apiforge.com" class="inline-block rounded-lg bg-emerald-500 px-8 py-3.5 font-bold text-black hover:bg-emerald-400 transition text-lg">
            advertise@apiforge.com
        </a>
        <p class="mt-4 text-xs text-gray-600">Response within 24 hours. Custom packages available for annual commitments.</p>
    </div>
</section>
@endsection
