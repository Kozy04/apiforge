<div class="mx-auto max-w-xl">
    <div class="relative">
        <input type="text" id="model-search"
               class="w-full rounded-xl border border-gray-700 bg-gray-800/50 px-5 py-3.5 text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
               placeholder="Search models... (e.g. GPT-4o, Claude, Llama)"
               autocomplete="off">
        <div id="search-results" class="absolute left-0 right-0 top-full mt-2 rounded-xl border border-gray-700 bg-gray-900 shadow-2xl hidden z-50 max-h-80 overflow-y-auto"></div>
    </div>
</div>

<script>
(function() {
    const input = document.getElementById('model-search');
    const results = document.getElementById('search-results');
    let debounce;

    input.addEventListener('input', function() {
        clearTimeout(debounce);
        const q = this.value.trim();

        if (q.length < 2) {
            results.classList.add('hidden');
            return;
        }

        debounce = setTimeout(() => {
            fetch('/api/search?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        results.innerHTML = '<div class="p-4 text-sm text-gray-500">No models found.</div>';
                    } else {
                        results.innerHTML = data.map(m => `
                            <a href="/api-cost/${m.slug}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-800 transition text-left">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-white">${m.name}</p>
                                    <p class="text-xs text-gray-400">${m.provider}</p>
                                </div>
                                <span class="text-xs text-emerald-400 font-mono">$${parseFloat(m.input_cost).toFixed(2)}/M</span>
                            </a>
                        `).join('');
                    }
                    results.classList.remove('hidden');
                })
                .catch(() => {
                    results.innerHTML = '<div class="p-4 text-sm text-gray-500">Search unavailable.</div>';
                    results.classList.remove('hidden');
                });
        }, 250);
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !results.contains(e.target)) {
            results.classList.add('hidden');
        }
    });
})();
</script>
