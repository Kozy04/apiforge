<?php

namespace Database\Seeders;

use App\Models\ApiModel;
use App\Models\Provider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ApiModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            'openai' => [
                ['name' => 'GPT-4o',           'slug' => 'gpt-4o',           'input_cost_per_m' => 2.50,  'output_cost_per_m' => 10.00, 'context_window' => 128000, 'latency_score' => 0.8],
                ['name' => 'GPT-4o mini',      'slug' => 'gpt-4o-mini',      'input_cost_per_m' => 0.15,  'output_cost_per_m' => 0.60,  'context_window' => 128000, 'latency_score' => 0.5],
                ['name' => 'GPT-4.1',          'slug' => 'gpt-4-1',          'input_cost_per_m' => 2.00,  'output_cost_per_m' => 8.00,  'context_window' => 1000000,'latency_score' => 1.0],
                ['name' => 'o3',               'slug' => 'o3',               'input_cost_per_m' => 10.00, 'output_cost_per_m' => 40.00, 'context_window' => 200000, 'latency_score' => 3.0],
                ['name' => 'o4-mini',          'slug' => 'o4-mini',          'input_cost_per_m' => 1.10,  'output_cost_per_m' => 4.40,  'context_window' => 200000, 'latency_score' => 2.0],
            ],
            'anthropic' => [
                ['name' => 'Claude 3.5 Sonnet', 'slug' => 'claude-3-5-sonnet', 'input_cost_per_m' => 3.00,  'output_cost_per_m' => 15.00, 'context_window' => 200000, 'latency_score' => 1.0],
                ['name' => 'Claude 3.5 Haiku',  'slug' => 'claude-3-5-haiku',  'input_cost_per_m' => 0.80,  'output_cost_per_m' => 4.00,  'context_window' => 200000, 'latency_score' => 0.4],
                ['name' => 'Claude Opus 4',     'slug' => 'claude-opus-4',      'input_cost_per_m' => 15.00, 'output_cost_per_m' => 75.00, 'context_window' => 200000, 'latency_score' => 2.0],
                ['name' => 'Claude Sonnet 4',   'slug' => 'claude-sonnet-4',    'input_cost_per_m' => 3.00,  'output_cost_per_m' => 15.00, 'context_window' => 200000, 'latency_score' => 1.0],
            ],
            'google' => [
                ['name' => 'Gemini 2.5 Pro',   'slug' => 'gemini-2-5-pro',   'input_cost_per_m' => 1.25,  'output_cost_per_m' => 10.00, 'context_window' => 1000000,'latency_score' => 1.2],
                ['name' => 'Gemini 2.5 Flash', 'slug' => 'gemini-2-5-flash', 'input_cost_per_m' => 0.15,  'output_cost_per_m' => 0.60,  'context_window' => 1000000,'latency_score' => 0.3],
                ['name' => 'Gemini 2.0 Flash', 'slug' => 'gemini-2-0-flash', 'input_cost_per_m' => 0.10,  'output_cost_per_m' => 0.40,  'context_window' => 1000000,'latency_score' => 0.2],
            ],
            'groq' => [
                ['name' => 'Llama 3.3 70B',    'slug' => 'llama-3-3-70b',    'input_cost_per_m' => 0.59,  'output_cost_per_m' => 0.79,  'context_window' => 128000, 'latency_score' => 0.1],
                ['name' => 'Mixtral 8x7B',     'slug' => 'mixtral-8x7b',     'input_cost_per_m' => 0.27,  'output_cost_per_m' => 0.27,  'context_window' => 32768,  'latency_score' => 0.1],
                ['name' => 'Gemma 2 9B',       'slug' => 'gemma-2-9b',       'input_cost_per_m' => 0.20,  'output_cost_per_m' => 0.20,  'context_window' => 8192,   'latency_score' => 0.05],
            ],
            'mistral' => [
                ['name' => 'Mistral Large',    'slug' => 'mistral-large',    'input_cost_per_m' => 2.00,  'output_cost_per_m' => 6.00,  'context_window' => 128000, 'latency_score' => 1.5],
                ['name' => 'Mistral Small',    'slug' => 'mistral-small',    'input_cost_per_m' => 0.20,  'output_cost_per_m' => 0.60,  'context_window' => 32000,  'latency_score' => 0.3],
                ['name' => 'Codestral',        'slug' => 'codestral',        'input_cost_per_m' => 0.30,  'output_cost_per_m' => 0.90,  'context_window' => 256000, 'latency_score' => 0.4],
            ],
            'deepseek' => [
                ['name' => 'DeepSeek-V3',      'slug' => 'deepseek-v3',      'input_cost_per_m' => 0.27,  'output_cost_per_m' => 1.10,  'context_window' => 128000, 'latency_score' => 0.8],
                ['name' => 'DeepSeek-R1',      'slug' => 'deepseek-r1',      'input_cost_per_m' => 0.55,  'output_cost_per_m' => 2.19,  'context_window' => 128000, 'latency_score' => 3.0],
            ],
            'together-ai' => [
                ['name' => 'Llama 3.1 405B',   'slug' => 'llama-3-1-405b',   'input_cost_per_m' => 0.88,  'output_cost_per_m' => 0.88,  'context_window' => 131072, 'latency_score' => 0.6],
                ['name' => 'Mixtral 8x22B',    'slug' => 'mixtral-8x22b',    'input_cost_per_m' => 0.90,  'output_cost_per_m' => 0.90,  'context_window' => 65536,  'latency_score' => 0.4],
            ],
            'cohere' => [
                ['name' => 'Command R+',       'slug' => 'command-r-plus',   'input_cost_per_m' => 2.50,  'output_cost_per_m' => 10.00, 'context_window' => 128000, 'latency_score' => 1.0],
                ['name' => 'Embed v4',         'slug' => 'embed-v4',         'input_cost_per_m' => 0.10,  'output_cost_per_m' => 0.00,  'context_window' => 512,    'latency_score' => 0.05],
            ],
            'fireworks-ai' => [
                ['name' => 'Llama 3.1 70B',    'slug' => 'llama-3-1-70b',    'input_cost_per_m' => 0.90,  'output_cost_per_m' => 0.90,  'context_window' => 128000, 'latency_score' => 0.3],
                ['name' => 'Mixtral MoE',      'slug' => 'mixtral-moe',      'input_cost_per_m' => 0.50,  'output_cost_per_m' => 0.50,  'context_window' => 32768,  'latency_score' => 0.2],
            ],
            'replicate' => [
                ['name' => 'Llama 3.3 70B',    'slug' => 'replicate-llama-3-3-70b', 'input_cost_per_m' => 0.65, 'output_cost_per_m' => 0.75, 'context_window' => 128000, 'latency_score' => 1.0],
                ['name' => 'Flux Pro',         'slug' => 'flux-pro',                 'input_cost_per_m' => 0.00, 'output_cost_per_m' => 0.00, 'context_window' => 0,     'latency_score' => 5.0],
            ],
        ];

        $providers = Provider::all()->keyBy('slug');
        $now = Carbon::now();

        foreach ($models as $providerSlug => $modelList) {
            $provider = $providers->get($providerSlug);
            if (!$provider) continue;

            foreach ($modelList as $model) {
                ApiModel::updateOrCreate(
                    ['slug' => $model['slug']],
                    array_merge($model, [
                        'provider_id'  => $provider->id,
                        'last_updated' => $now,
                    ])
                );
            }
        }
    }
}
