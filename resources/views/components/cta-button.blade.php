<a href="{{ $url }}" target="_blank" rel="nofollow noopener"
   class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-5 py-2.5 font-semibold text-black hover:bg-emerald-400 transition text-sm">
    {{ $slot ?? 'Get API Key' }}
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
    </svg>
</a>
