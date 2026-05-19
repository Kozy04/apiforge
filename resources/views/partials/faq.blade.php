<div class="rounded-xl border border-blue-500/20 bg-gradient-to-br from-blue-500/5 to-gray-900 p-6 mt-8">
    <h2 class="text-lg font-bold text-white flex items-center gap-2 mb-4">
        <span class="text-blue-400">&#63;</span> Frequently Asked Questions
    </h2>

    <div class="space-y-4 text-sm">
        <div>
            <h3 class="font-semibold text-white mb-1">How much does {{ $model->name }} cost?</h3>
            <p class="text-gray-400 leading-relaxed">
                {{ $model->name }} costs <strong class="text-white">${{ number_format($model->input_cost_per_m, 6) }}</strong> per 1 million input tokens
                and <strong class="text-white">${{ number_format($model->output_cost_per_m, 6) }}</strong> per 1 million output tokens.
                For a typical 5M token monthly workload (70% input, 30% output), the estimated cost is
                <strong class="text-emerald-400">${{ number_format((5 * 0.7 * $model->input_cost_per_m) + (5 * 0.3 * $model->output_cost_per_m), 2) }}/month</strong>.
            </p>
        </div>

        @if($model->context_window > 200000)
        <div>
            <h3 class="font-semibold text-white mb-1">Is {{ $model->name }} good for long documents?</h3>
            <p class="text-gray-400 leading-relaxed">
                Yes — with a <strong class="text-white">{{ number_format($model->context_window) }}-token context window</strong>,
                {{ $model->name }} can process entire books, large codebases, or months of conversation history in a single context.
                This is {{ $model->context_window >= 1000000 ? 'industry-leading' : 'competitive' }} compared to other models.
            </p>
        </div>
        @endif

        @if($model->input_cost_per_m < 1)
        <div>
            <h3 class="font-semibold text-white mb-1">Is {{ $model->name }} one of the cheapest AI model APIs?</h3>
            <p class="text-gray-400 leading-relaxed">
                Yes — at <strong class="text-white">${{ number_format($model->input_cost_per_m, 2) }}/M input</strong>,
                {{ $model->name }} is among the most affordable models available. Compare it with
                <a href="{{ route('compare.show', ['model_one' => $model->slug, 'model_two' => 'gemini-2-5-flash']) }}" class="text-emerald-400 hover:underline">
                    Gemini 2.5 Flash ($0.15/M) &rarr;</a>.
            </p>
        </div>
        @endif

        @if($model->latency_score < 0.5)
        <div>
            <h3 class="font-semibold text-white mb-1">How fast is {{ $model->name }}?</h3>
            <p class="text-gray-400 leading-relaxed">
                {{ $model->name }} has an average latency of <strong class="text-white">{{ $model->latency_score }}s</strong>,
                making it one of the fastest models available. Excellent for real-time applications like chatbots and interactive tools.
            </p>
        </div>
        @endif

        <div>
            <h3 class="font-semibold text-white mb-1">How does {{ $model->name }} pricing compare to alternatives?</h3>
            <p class="text-gray-400 leading-relaxed">
                {{ $model->name }} is provided by <strong class="text-white">{{ $model->provider->name }}</strong> at
                ${{ number_format($model->input_cost_per_m, 2) }}/M input. Compare it with competitors:
            </p>
            <ul class="mt-2 space-y-1 text-gray-400">
                @php
                $alternatives = \App\Models\ApiModel::with('provider')
                    ->where('id', '!=', $model->id)
                    ->whereIn('provider_id', \App\Models\ApiModel::where('id', '!=', $model->id)->select('provider_id')->distinct()->limit(4)->pluck('provider_id'))
                    ->inRandomOrder()
                    ->limit(3)
                    ->get();
                @endphp
                @foreach($alternatives as $alt)
                <li>&bull; <a href="{{ route('compare.show', ['model_one' => $model->slug, 'model_two' => $alt->slug]) }}" class="text-emerald-400 hover:underline">
                    {{ $model->name }} vs {{ $alt->name }}</a> — {{ $alt->name }}: ${{ number_format($alt->input_cost_per_m, 2) }}/M ({{ $alt->provider->name }})
                </li>
                @endforeach
            </ul>
        </div>

        <div>
            <h3 class="font-semibold text-white mb-1">What is {{ $model->name }} best used for?</h3>
            <p class="text-gray-400 leading-relaxed">
                @if($model->context_window > 500000)
                {{ $model->name }} excels at <strong class="text-white">long-context analysis</strong>, document processing, and knowledge-intensive tasks.
                @elseif($model->input_cost_per_m < 0.5)
                {{ $model->name }} is ideal for <strong class="text-white">high-volume, cost-sensitive workloads</strong> like chatbots, content moderation, and text classification.
                @elseif($model->input_cost_per_m > 2)
                {{ $model->name }} is best for <strong class="text-white">complex reasoning, code generation, and enterprise applications</strong> where quality matters more than cost.
                @else
                {{ $model->name }} is a <strong class="text-white">balanced general-purpose model</strong> suitable for a wide range of tasks from coding to content generation.
                @endif
            </p>
        </div>
    </div>
</div>
