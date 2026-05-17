<?php

namespace App\Console\Commands;

use App\Models\ApiModel;
use App\Models\BlogPost;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'app:generate-sitemap';
    protected $description = 'Generate sitemap.xml with all model, comparison, and blog pages';

    public function handle(): int
    {
        $sitemap = Sitemap::create();
        $baseUrl = config('app.url');

        $sitemap->add(Url::create($baseUrl)
            ->setPriority(1.0)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        $models = ApiModel::with('provider')->get();

        foreach ($models as $model) {
            $sitemap->add(Url::create(route('model.show', $model))
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setLastModificationDate($model->updated_at));
        }

        $modelSlugs = $models->pluck('slug')->toArray();
        $count = count($modelSlugs);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $sitemap->add(Url::create(route('compare.show', [
                    'model_one' => $modelSlugs[$i],
                    'model_two' => $modelSlugs[$j],
                ]))
                ->setPriority(0.8)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            }
        }

        $sitemap->add(Url::create(route('blog.index'))
            ->setPriority(0.9)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        foreach (BlogPost::published()->get() as $post) {
            $sitemap->add(Url::create(route('blog.show', $post))
                ->setPriority(0.7)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setLastModificationDate($post->updated_at));
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

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
        $json = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'AI Model API Pricing Directory',
            'numberOfItems' => $models->count(),
            'itemListElement' => $items,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        file_put_contents(public_path('schema-index.json'), $json);

        $blogCount = BlogPost::published()->count();
        $this->info('Sitemap generated: ' . public_path('sitemap.xml'));
        $this->info("Pages: 1 home + {$count} models + " . ($count * ($count - 1) / 2) . " comparisons + {$blogCount} blog");

        return self::SUCCESS;
    }
}
