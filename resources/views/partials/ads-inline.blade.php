<div id="partial-ads-inline">
    @production
    @if(env('CARBON_ADS_ID'))
    <div class="mt-8 rounded-xl border border-gray-800 bg-gray-900/20 p-4 text-center">
        <p class="text-[10px] text-gray-600 uppercase tracking-widest mb-2">Sponsored</p>
        <script async src="//cdn.carbonads.com/carbon.js?serve={{ env('CARBON_ADS_ID') }}&placement={{ parse_url(config('app.url'), PHP_URL_HOST) }}inline" id="_carbonads_js_inline"></script>
        <div class="min-h-[90px] flex items-center justify-center text-xs text-gray-600">Loading...</div>
    </div>
    @endif
    @endproduction
</div>
