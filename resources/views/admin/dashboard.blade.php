<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard — APIForge</title>
    @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    <link rel="stylesheet" href="/build/{{ $manifest['resources/css/app.css']['file'] }}">
</head>
<body class="bg-gray-950 text-gray-100">
    <div class="flex min-h-screen">
        <aside class="w-60 border-r border-gray-800 bg-gray-900 p-6 hidden lg:flex lg:flex-col">
            <div class="mb-8">
                <h2 class="text-xl font-bold text-emerald-400">API<span class="text-white">Forge</span></h2>
                <p class="text-xs text-gray-600 mt-1">Admin Panel</p>
            </div>
            <nav class="flex-1 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded-lg bg-emerald-500/10 px-3 py-2.5 text-emerald-400 font-medium">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ url('/') }}" target="_blank" class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-gray-400 hover:text-white hover:bg-gray-800/50 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Site
                </a>
                <a href="{{ route('admin.logout') }}" class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition mt-4">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-6 lg:p-8 overflow-x-hidden">
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-white">Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ now()->format('l, F d, Y') }}</p>
                </div>
                <span class="text-xs text-gray-600 bg-gray-900 border border-gray-800 rounded-lg px-3 py-1.5 font-mono">
                    APIForge v1.0
                </span>
            </div>

            @if(session('status'))
                <div class="mb-6 rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm text-emerald-400 flex items-center gap-2">
                    <span>&check;</span> {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 mb-8">
                <x-admin-stat label="Total Models" value="{{ $totalModels }}" />
                <x-admin-stat label="Providers" value="{{ $totalProviders }}" />
                <x-admin-stat label="Clicks (30d)" value="{{ $monthClicks }}" />
                <x-admin-stat label="Leads (30d)" value="{{ $monthLeads }}" />
                <x-admin-stat label="Subscribers" value="{{ $totalSubscribers }}" />
            </div>

            <div class="grid gap-6 lg:grid-cols-2 mb-8">
                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6">
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wide mb-4">Revenue Estimate</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Total affiliate clicks</span>
                            <span class="font-mono text-white font-medium">{{ number_format($totalClicks) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Est. conversions (3%)</span>
                            <span class="font-mono text-white">{{ round($totalClicks * 0.03) }}</span>
                        </div>
                        <hr class="border-gray-800">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Est. leads value</span>
                            <span class="font-mono text-emerald-400 font-bold text-lg">${{ number_format($totalLeads * 100) }}</span>
                        </div>
                        <p class="text-xs text-gray-600">Estimated at $100 per qualified lead</p>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6">
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wide mb-4">Top Providers by Clicks</h3>
                    <div class="space-y-2">
                        @forelse($topProviders->take(8) as $p)
                            <div class="flex justify-between items-center text-sm py-1.5 border-b border-gray-800/50 last:border-0">
                                <span class="text-gray-300">{{ $p->name }}</span>
                                <span class="font-mono text-white text-xs bg-gray-800 rounded px-2 py-0.5">{{ $p->clicks_count }} clicks</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 py-4">No clicks recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6">
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wide mb-4">Recent Leads</h3>
                    <div class="overflow-x-auto -mx-2">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 text-xs uppercase tracking-wide">
                                    <th class="pb-3 pr-4 font-medium">Name</th>
                                    <th class="pb-3 pr-4 font-medium">Company</th>
                                    <th class="pb-3 pr-4 font-medium">Tokens</th>
                                    <th class="pb-3 pr-4 font-medium">Status</th>
                                    <th class="pb-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeads as $lead)
                                    <tr class="border-t border-gray-800/50 hover:bg-gray-800/30 transition">
                                        <td class="py-3 pr-4 text-white font-medium">{{ $lead->name }}</td>
                                        <td class="py-3 pr-4 text-gray-400">{{ $lead->company ?: '—' }}</td>
                                        <td class="py-3 pr-4 font-mono text-gray-300 text-xs">{{ $lead->monthly_tokens ? number_format($lead->monthly_tokens) : '—' }}</td>
                                        <td class="py-3 pr-4">
                                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium
                                                {{ $lead->status === 'new' ? 'bg-blue-500/10 text-blue-400' : '' }}
                                                {{ $lead->status === 'contacted' ? 'bg-yellow-500/10 text-yellow-400' : '' }}
                                                {{ $lead->status === 'sold' ? 'bg-emerald-500/10 text-emerald-400' : '' }}">
                                                {{ ucfirst($lead->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <form method="POST" action="{{ route('admin.leads.update', $lead->id) }}" class="flex gap-1.5">
                                                @csrf
                                                <button name="status" value="contacted" class="text-xs px-2 py-1 rounded bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20 transition">Contacted</button>
                                                <button name="status" value="sold" class="text-xs px-2 py-1 rounded bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition">Sold</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-500">
                                            <p>No leads yet.</p>
                                            <p class="text-xs mt-1">Leads appear when visitors submit the "Get 3 Free Quotes" form.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6">
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wide mb-4">Recent Clicks</h3>
                    <div class="overflow-x-auto -mx-2">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 text-xs uppercase tracking-wide">
                                    <th class="pb-3 pr-4 font-medium">Provider</th>
                                    <th class="pb-3 pr-4 font-medium">Model</th>
                                    <th class="pb-3 pr-4 font-medium">Page</th>
                                    <th class="pb-3 font-medium">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentClicks as $click)
                                    <tr class="border-t border-gray-800/50 hover:bg-gray-800/30 transition">
                                        <td class="py-3 pr-4 text-white font-medium">{{ $click->provider->name }}</td>
                                        <td class="py-3 pr-4 text-gray-400">{{ $click->apiModel?->name ?? '—' }}</td>
                                        <td class="py-3 pr-4 text-gray-500 text-xs font-mono max-w-[120px] truncate">{{ Str::limit($click->page, 25) }}</td>
                                        <td class="py-3 text-gray-500 text-xs">{{ $click->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-gray-500">
                                            <p>No clicks recorded yet.</p>
                                            <p class="text-xs mt-1">Clicks appear when visitors click affiliate links.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
