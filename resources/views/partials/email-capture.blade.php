<div class="rounded-xl border border-gray-800 bg-gray-900/50 p-6">
    <p class="text-base font-semibold text-white">Get price drop alerts</p>
    <p class="text-sm text-gray-400 mt-1">We'll email you when API costs change.</p>
    <form id="subscribe-form" class="mt-4 flex flex-col sm:flex-row gap-3" onsubmit="submitSubscribe(event)">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="email" name="email" required placeholder="your@email.com"
               class="flex-1 min-w-0 rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 text-base focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition">
        <button type="submit"
                class="shrink-0 rounded-lg bg-emerald-500 px-6 py-3 text-base font-semibold text-black hover:bg-emerald-400 transition whitespace-nowrap cursor-pointer">
            Subscribe
        </button>
    </form>
    <p id="subscribe-msg" class="text-sm text-emerald-400 mt-3 hidden"></p>
</div>

<script>
function submitSubscribe(e) {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button');
    const msg = document.getElementById('subscribe-msg');
    btn.disabled = true;

    const data = new FormData(form);
    fetch('/api/subscribe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': data.get('_token') },
        body: JSON.stringify({ email: data.get('email') })
    })
    .then(r => r.json())
    .then(res => {
        msg.textContent = res.message;
        msg.classList.remove('hidden');
        form.reset();
    })
    .finally(() => { btn.disabled = false; });
}
</script>
