<div class="rounded-xl border border-emerald-500/30 bg-gradient-to-br from-emerald-500/5 to-gray-900 p-6 sm:p-8" id="lead-form">
    <h3 class="text-xl font-bold text-white flex items-center gap-2">
        <span class="text-emerald-400">&#9733;</span> Get 3 Free API Quotes
    </h3>
    <p class="mt-2 text-gray-400 text-base">Enterprise buyers: we'll match you with the best-priced providers for your volume. No spam, no commitment.</p>

    <form id="lead-gen-form" class="mt-6 space-y-4" onsubmit="submitLead(event)">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Full name</label>
                <input type="text" name="name" required placeholder="John Smith"
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3.5 text-white placeholder-gray-500 text-base focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Work email</label>
                <input type="email" name="email" required placeholder="john@company.com"
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3.5 text-white placeholder-gray-500 text-base focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Company name</label>
                <input type="text" name="company" placeholder="Acme Inc."
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3.5 text-white placeholder-gray-500 text-base focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Monthly token volume</label>
                <input type="number" name="monthly_tokens" placeholder="e.g. 10,000,000"
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3.5 text-white placeholder-gray-500 text-base focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Primary use case</label>
            <select name="use_case"
                    class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3.5 text-white text-base focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition">
                <option value="">Select use case...</option>
                <option value="chatbot">Chatbot / Customer Support</option>
                <option value="code-gen">Code Generation</option>
                <option value="content">Content / Marketing</option>
                <option value="enterprise">Enterprise Knowledge Base</option>
                <option value="other">Other</option>
            </select>
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-emerald-500 py-3.5 font-semibold text-black hover:bg-emerald-400 transition text-base cursor-pointer">
            Get Matched with 3 Providers
        </button>

        <p id="lead-form-msg" class="text-sm text-center hidden mt-3"></p>
    </form>
</div>

<script>
function submitLead(e) {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type=submit]');
    const msg = document.getElementById('lead-form-msg');
    btn.disabled = true;
    btn.textContent = 'Submitting...';

    const data = new FormData(form);
    const json = Object.fromEntries(data.entries());
    json.preferred_providers = ['{{ $model->provider->slug ?? '' }}'];

    fetch('/api/leads', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': data.get('_token') },
        body: JSON.stringify(json)
    })
    .then(r => r.json())
    .then(res => {
        msg.textContent = res.message;
        msg.className = 'text-sm text-center text-emerald-400 mt-3';
        form.reset();
    })
    .catch(() => {
        msg.textContent = 'Something went wrong. Please try again.';
        msg.className = 'text-sm text-center text-red-400 mt-3';
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Get Matched with 3 Providers';
        msg.classList.remove('hidden');
    });
}
</script>
