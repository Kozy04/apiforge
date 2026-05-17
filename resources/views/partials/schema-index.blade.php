@php
$items = [];
foreach ($models as $i => $model) {
    $items[] = [
        '@type' => 'ListItem',
        'position' => $i + 1,
        'item' => [
            '@type' => 'SoftwareApplication',
            'name' => $model->name,
            'url' => route('model.show', $model),
            'offers' => [
                '@type' => 'Offer',
                'price' => (string) $model->input_cost_per_m,
                'priceCurrency' => 'USD',
                'unitText' => 'per 1M input tokens',
            ],
            'provider' => [
                '@type' => 'Organization',
                'name' => $model->provider->name,
            ],
        ],
    ];
}
$ldJson = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'AI Model API Pricing Directory',
    'description' => 'Compare pricing for all major AI model APIs: OpenAI, Anthropic, Google, Groq, Mistral, DeepSeek, and more.',
    'numberOfItems' => $models->count(),
    'itemListElement' => $items,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
<script type="application/ld+json">{!! $ldJson !!}</script>
