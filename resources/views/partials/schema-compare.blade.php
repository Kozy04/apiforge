@php
$ldJson = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => "{$modelOne->name} vs {$modelTwo->name}: Pricing Comparison 2025",
    'description' => "Compare {$modelOne->name} (\$".number_format($modelOne->input_cost_per_m, 2)."/M input) vs {$modelTwo->name} (\$".number_format($modelTwo->input_cost_per_m, 2)."/M input). Side-by-side pricing, specs, and cost calculator.",
    'about' => [
        [
            '@type' => 'SoftwareApplication',
            'name' => $modelOne->name,
            'offers' => [
                '@type' => 'Offer',
                'price' => (string) $modelOne->input_cost_per_m,
                'priceCurrency' => 'USD',
                'unitText' => 'per 1M input tokens',
            ],
            'provider' => [
                '@type' => 'Organization',
                'name' => $modelOne->provider->name,
            ],
        ],
        [
            '@type' => 'SoftwareApplication',
            'name' => $modelTwo->name,
            'offers' => [
                '@type' => 'Offer',
                'price' => (string) $modelTwo->input_cost_per_m,
                'priceCurrency' => 'USD',
                'unitText' => 'per 1M input tokens',
            ],
            'provider' => [
                '@type' => 'Organization',
                'name' => $modelTwo->provider->name,
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
<script type="application/ld+json">{!! $ldJson !!}</script>
