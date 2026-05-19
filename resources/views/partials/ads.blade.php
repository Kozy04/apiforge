<div id="partial-ads">
    @production
    @php $carbonId = env('CARBON_ADS_ID'); @endphp
    @if($carbonId)
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
        <div class="rounded-xl border border-gray-800 bg-gray-900/30 p-4 text-center">
            <p class="text-[10px] text-gray-600 uppercase tracking-widest mb-2">Sponsored</p>
            <script async src="//cdn.carbonads.com/carbon.js?serve={{ $carbonId }}&placement={{ parse_url(config('app.url'), PHP_URL_HOST) }}" id="_carbonads_js"></script>
            <div id="carbon-block" class="min-h-[100px] flex items-center justify-center">
                <p class="text-xs text-gray-700">Loading ad...</p>
            </div>
        </div>
    </div>
    @else
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
        <div class="rounded-xl border border-gray-800 bg-gray-900/10 p-4 text-center">
            <p class="text-[10px] text-gray-600 uppercase tracking-widest">Advertisement</p>
            <div class="mt-2 h-24 flex items-center justify-center">
                <a href="https://www.carbonads.net/" target="_blank" rel="nofollow noopener" class="text-xs text-gray-600 hover:text-gray-400 transition">
                    Advertise here via Carbon Ads
                </a>
            </div>
        </div>
    </div>
    @endif
    @endproduction
</div>
