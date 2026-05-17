<!DOCTYPE html>
<html lang="en" class="bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard — APIForge</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-950 text-gray-100">
    <div class="flex min-h-screen">
        <aside class="w-56 border-r border-gray-800 bg-gray-900 p-5 hidden lg:block">
            <h2 class="text-lg font-bold text-emerald-400 mb-8">API<span class="text-white">Forge</span></h2>
            <nav class="space-y-2 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg bg-emerald-500/10 px-3 py-2 text-emerald-400 font-medium">Dashboard</a>
                <a href="{{ route('admin.logout') }}" class="block rounded-lg px-3 py-2 text-gray-400 hover:text-white transition">Logout</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white">Dashboard</h1>
                <span class="text-sm text-gray-500">{{ now()->toDateString() }}</span>
            </div>

            @if(session('status'))
                <div class="mb-4 rounded-lg bg-emerald-500/10 border border-emerald-500/30 px-4 py-2 text-sm text-emerald-400">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 mb-8">
                <x-admin-stat label="Models" value="{{ $totalModels }}" />
                <x-admin-stat label="Providers" value="{{ $totalProviders }}" />
                <x-admin-stat label="Clicks (30d)" value="{{ $monthClicks }}" />
                <x-admin-stat label="Leads (30d)" value="{{ $monthLeads }}" />
                <x-admin-stat label="Subscribers" value="{{ $totalSubscribers }}" />
            </div>

            <div class="grid gap-6 lg:grid-cols-2 mb-8">
                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-5">
                    <h3 class="font-semibold text-white mb-3">Revenue Estimate</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total affiliate clicks</span>
                            <span class="font-mono text-white">{{ $totalClicks }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Est. conversions (3%)</span>
                            <span class="font-mono text-white">{{ round($totalClicks * 0.03) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Est. leads value ($100/lead)</span>
                            <span class="font-mono text-emerald-400 font-bold">${{ number_format($totalLeads * 100) }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-5">
                    <h3 class="font-semibold text-white mb-3">Top Providers by Clicks</h3>
                    <div class="space-y-2">
                        @forelse($topProviders as $p)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-300">{{ $p->name }}</span>
                                <span class="font-mono text-white">{{ $p->clicks_count }} clicks</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No clicks yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-5">
                    <h3 class="font-semibold text-white mb-3">Recent Leads</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="pb-2 pr-4">Name</th>
                                    <th class="pb-2 pr-4">Company</th>
                                    <th class="pb-2 pr-4">Tokens</th>
                                    <th class="pb-2 pr-4">Status</th>
                                    <th class="pb-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeads as $lead)
                                    <tr class="border-t border-gray-800">
                                        <td class="py-2 pr-4 text-white">{{ $lead->name }}</td>
                                        <td class="py-2 pr-4 text-gray-400">{{ $lead->company ?: '-' }}</td>
                                        <td class="py-2 pr-4 font-mono text-gray-300">{{ $lead->monthly_tokens ? number_format($lead->monthly_tokens) : '-' }}</td>
                                        <td class="py-2 pr-4">
                                            <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                                {{ $lead->status === 'new' ? 'bg-blue-500/10 text-blue-400' : '' }}
                                                {{ $lead->status === 'contacted' ? 'bg-yellow-500/10 text-yellow-400' : '' }}
                                                {{ $lead->status === 'sold' ? 'bg-emerald-500/10 text-emerald-400' : '' }}">
                                                {{ $lead->status }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <form method="POST" action="{{ route('admin.leads.update', $lead->id) }}" class="flex gap-1">
                                                @csrf
                                                <button name="status" value="contacted" class="text-xs text-yellow-400 hover:underline">Contacted</button>
                                                <button name="status" value="sold" class="text-xs text-emerald-400 hover:underline">Sold</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-4 text-gray-500">No leads yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-5">
                    <h3 class="font-semibold text-white mb-3">Recent Clicks</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="pb-2 pr-4">Provider</th>
                                    <th class="pb-2 pr-4">Model</th>
                                    <th class="pb-2 pr-4">Page</th>
                                    <th class="pb-2">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentClicks as $click)
                                    <tr class="border-t border-gray-800">
                                        <td class="py-2 pr-4 text-white">{{ $click->provider->name }}</td>
                                        <td class="py-2 pr-4 text-gray-400">{{ $click->apiModel?->name ?? '-' }}</td>
                                        <td class="py-2 pr-4 text-gray-500 text-xs font-mono">{{ Str::limit($click->page, 30) }}</td>
                                        <td class="py-2 text-gray-500">{{ $click->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-4 text-gray-500">No clicks yet.</td></tr>
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
