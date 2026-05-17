@php
$ldJson = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => $model->name,
    'applicationCategory' => 'AIApplication',
    'operatingSystem' => 'Cloud',
    'description' => "{$model->name} API pricing: \$".number_format($model->input_cost_per_m, 2)."/M input tokens, \$".number_format($model->output_cost_per_m, 2)."/M output tokens. ".number_format($model->context_window)." token context window.",
    'offers' => [
        '@type' => 'Offer',
        'price' => (string) $model->input_cost_per_m,
        'priceCurrency' => 'USD',
        'unitText' => 'per 1M input tokens',
        'priceSpecification' => [
            [
                '@type' => 'UnitPriceSpecification',
                'price' => (string) $model->input_cost_per_m,
                'priceCurrency' => 'USD',
                'unitText' => '1M input tokens',
                'referenceQuantity' => [
                    '@type' => 'QuantitativeValue',
                    'value' => '1000000',
                    'unitText' => 'tokens',
                ],
            ],
            [
                '@type' => 'UnitPriceSpecification',
                'price' => (string) $model->output_cost_per_m,
                'priceCurrency' => 'USD',
                'unitText' => '1M output tokens',
                'referenceQuantity' => [
                    '@type' => 'QuantitativeValue',
                    'value' => '1000000',
                    'unitText' => 'tokens',
                ],
            ],
        ],
    ],
    'provider' => [
        '@type' => 'Organization',
        'name' => $model->provider->name,
        'url' => $model->provider->affiliate_url,
    ],
    'dateModified' => $model->updated_at->toIso8601String(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
<script type="application/ld+json">{!! $ldJson !!}</script>
