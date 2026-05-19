<a href="{{ route('model.show', $model) }}"
   class="group block rounded-xl border border-gray-800 bg-gray-900/50 p-5 hover:border-emerald-500/50 hover:bg-gray-900 transition">
    <div class="flex items-start justify-between">
        <div>
            <h3 class="font-semibold text-white group-hover:text-emerald-400 transition">
                {{ $model->name }}
            </h3>
            <p class="mt-1 text-xs text-gray-400">{{ $model->provider->name }}</p>
        </div>
        <div class="flex flex-col items-end gap-1">
            @if(isset($highlight))
                <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-400">{{ $highlight }}</span>
            @endif
            <span class="rounded-full bg-gray-800 px-2 py-0.5 text-[10px] font-medium text-gray-400">
                {{ $model->context_window > 0 ? floor($model->context_window / 1000) . 'K' : 'N/A' }}
            </span>
        </div>
    </div>
    <div class="mt-3 flex gap-4 text-sm">
        <div>
            <span class="text-gray-500">Input</span>
            <p class="font-mono text-white">${{ number_format($model->input_cost_per_m, 2) }}/M</p>
        </div>
        <div>
            <span class="text-gray-500">Output</span>
            <p class="font-mono text-white">${{ number_format($model->output_cost_per_m, 2) }}/M</p>
        </div>
    </div>
    @php $tags = $model->tags; @endphp
    @if(count($tags))
    <div class="mt-3 flex flex-wrap gap-1">
        @foreach(array_slice($tags, 0, 3) as $tag)
            <span class="rounded-full bg-gray-800 px-2 py-0.5 text-[10px] font-medium
                {{ str_contains($tag, 'Budget') ? 'text-yellow-400' : '' }}
                {{ str_contains($tag, 'Fast') ? 'text-purple-400' : '' }}
                {{ str_contains($tag, 'Enterprise') ? 'text-red-400' : '' }}
                {{ str_contains($tag, 'Context') ? 'text-blue-400' : '' }}
                {{ str_contains($tag, 'Efficient') ? 'text-emerald-400' : '' }}
                {{ str_contains($tag, 'Value') ? 'text-orange-400' : '' }}">
                {{ $tag }}
            </span>
        @endforeach
    </div>
    @endif
</a>
