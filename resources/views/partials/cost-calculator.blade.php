<div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6">
    <h2 class="text-lg font-semibold text-white mb-4">
        <span class="text-emerald-400">&sum;</span> Live Cost Calculator
    </h2>

    @if(isset($single) && $single)
        <div id="calc-single">
            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-1">Monthly token usage</label>
                <input type="number" id="calc-tokens"
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                       placeholder="e.g. 5000000" min="0" step="100000">
                <p class="mt-1 text-xs text-gray-500">Enter your expected monthly tokens (input + output)</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-1">Input / Output split (%)</label>
                <input type="range" id="calc-split" min="10" max="90" value="70" class="w-full accent-emerald-500">
                <div class="flex justify-between text-xs text-gray-500">
                    <span id="calc-split-label">70% input / 30% output</span>
                </div>
            </div>
            <div class="rounded-lg bg-gray-800/50 p-4">
                <div class="flex justify-between items-baseline">
                    <span class="text-gray-400">Estimated monthly cost</span>
                    <span id="calc-result" class="text-2xl font-bold text-emerald-400">$0.00</span>
                </div>
            </div>
        </div>
    @else
        @php $modelOne = $modelOne ?? null; $modelTwo = $modelTwo ?? null; @endphp
        <div id="calc-dual">
            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-1">Monthly token usage</label>
                <input type="number" id="calc-tokens-dual"
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                       placeholder="e.g. 5000000" min="0" step="100000">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-gray-800/50 p-4">
                    <span class="text-sm text-gray-400">{{ $modelOne->name ?? 'Model A' }}</span>
                    <p id="calc-dual-result-a" class="text-xl font-bold text-white mt-1">$0.00</p>
                </div>
                <div class="rounded-lg bg-gray-800/50 p-4">
                    <span class="text-sm text-gray-400">{{ $modelTwo->name ?? 'Model B' }}</span>
                    <p id="calc-dual-result-b" class="text-xl font-bold text-white mt-1">$0.00</p>
                </div>
            </div>
        </div>
    @endif
</div>
