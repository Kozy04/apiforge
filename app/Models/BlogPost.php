<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content',
        'category', 'source_name', 'source_url',
        'is_published', 'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function renderedContent(): string
    {
        $html = Str::markdown($this->content);

        return preg_replace_callback(
            '/\b(GPT-4[o]?|GPT-4\.1|o3|o4-mini|Claude\s*(3\.5|Opus|Sonnet)\s*\d*|Gemini\s*2\.\d\s*(Pro|Flash)|Llama\s*3\.\d\s*\d*B|Mixtral\s*\d+x\d+B|DeepSeek[ -][VR]\d|Mistral\s*(Large|Small)|Codestral|Command\s*R\+?|Gemma\s*2\s*\d*B)\b/i',
            function ($matches) {
                $slug = \App\Models\ApiModel::where('name', 'like', "%{$matches[0]}%")->value('slug');
                return $slug
                    ? "<a href=\"" . route('model.show', $slug) . "\" class=\"text-emerald-400 hover:underline\">{$matches[0]}</a>"
                    : $matches[0];
            },
            $html
        );
    }
}
