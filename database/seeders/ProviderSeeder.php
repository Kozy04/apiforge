<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            ['name' => 'OpenAI',       'slug' => 'openai',       'affiliate_url' => 'https://platform.openai.com/signup'],
            ['name' => 'Anthropic',    'slug' => 'anthropic',    'affiliate_url' => 'https://console.anthropic.com/'],
            ['name' => 'Google',       'slug' => 'google',       'affiliate_url' => 'https://aistudio.google.com/'],
            ['name' => 'Groq',         'slug' => 'groq',         'affiliate_url' => 'https://console.groq.com/'],
            ['name' => 'Mistral',      'slug' => 'mistral',      'affiliate_url' => 'https://console.mistral.ai/'],
            ['name' => 'DeepSeek',     'slug' => 'deepseek',     'affiliate_url' => 'https://platform.deepseek.com/'],
            ['name' => 'Together AI',  'slug' => 'together-ai',  'affiliate_url' => 'https://www.together.ai/'],
            ['name' => 'Cohere',       'slug' => 'cohere',       'affiliate_url' => 'https://dashboard.cohere.com/'],
            ['name' => 'Fireworks AI', 'slug' => 'fireworks-ai', 'affiliate_url' => 'https://fireworks.ai/'],
            ['name' => 'Replicate',    'slug' => 'replicate',    'affiliate_url' => 'https://replicate.com/'],
            ['name' => 'Stability AI', 'slug' => 'stability-ai', 'affiliate_url' => 'https://platform.stability.ai/'],
            ['name' => 'Midjourney',   'slug' => 'midjourney',   'affiliate_url' => 'https://www.midjourney.com/'],
            ['name' => 'Runway',       'slug' => 'runway',       'affiliate_url' => 'https://runwayml.com/'],
            ['name' => 'ElevenLabs',   'slug' => 'elevenlabs',   'affiliate_url' => 'https://elevenlabs.io/'],
            ['name' => 'Hugging Face', 'slug' => 'hugging-face', 'affiliate_url' => 'https://huggingface.co/'],
            ['name' => 'Ideogram',     'slug' => 'ideogram',     'affiliate_url' => 'https://ideogram.ai/'],
        ];

        foreach ($providers as $provider) {
            Provider::updateOrCreate(['slug' => $provider['slug']], $provider);
        }
    }
}
