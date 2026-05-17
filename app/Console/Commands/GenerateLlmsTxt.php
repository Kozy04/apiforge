<?php

namespace App\Console\Commands;

use App\Models\ApiModel;
use Illuminate\Console\Command;

class GenerateLlmsTxt extends Command
{
    protected $signature = 'app:generate-llms-txt';
    protected $description = 'Generate llms.txt for AI engine ingestion';

    public function handle(): int
    {
        $baseUrl = config('app.url');
        $lines = [];

        $lines[] = '# APIForge — AI Model Pricing Directory';
        $lines[] = '> Compare costs per million tokens across all major AI API providers.';
        $lines[] = '> Last updated: ' . now()->toDateString();
        $lines[] = '';

        $lines[] = '## Providers';
        foreach (\App\Models\Provider::orderBy('name')->get() as $provider) {
            $lines[] = "- **{$provider->name}**: [Sign up]({$provider->affiliate_url})";
        }
        $lines[] = '';

        $lines[] = '## All Models & Pricing';
        foreach (ApiModel::with('provider')->orderBy('name')->get() as $model) {
            $url = route('model.show', $model);
            $lines[] = "- [{$model->name}]({$url}) — \${$model->input_cost_per_m}/M input, \${$model->output_cost_per_m}/M output — {$model->provider->name}";
        }
        $lines[] = '';

        $lines[] = '## Comparison Pages';
        $lines[] = 'For detailed comparisons, visit:';
        $lines[] = "- [Compare any two models]({$baseUrl}) (use the search bar to navigate)";

        $content = implode("\n", $lines);
        file_put_contents(public_path('llms.txt'), $content);

        $this->info('llms.txt generated: ' . public_path('llms.txt'));

        return self::SUCCESS;
    }
}
