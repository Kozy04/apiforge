<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiModel extends Model
{
    protected $fillable = [
        'provider_id', 'name', 'slug',
        'input_cost_per_m', 'output_cost_per_m',
        'context_window', 'latency_score', 'last_updated',
        'category',
    ];

    protected $casts = [
        'input_cost_per_m' => 'float',
        'output_cost_per_m' => 'float',
        'context_window' => 'integer',
        'latency_score' => 'float',
        'last_updated' => 'datetime',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTagsAttribute(): array
    {
        $tags = [];
        if ($this->input_cost_per_m < 0.50) $tags[] = 'Budget Pick';
        elseif ($this->input_cost_per_m < 2) $tags[] = 'Value';
        elseif ($this->input_cost_per_m > 5) $tags[] = 'Enterprise';

        if ($this->context_window >= 500000) $tags[] = 'Long Context';
        elseif ($this->context_window >= 128000) $tags[] = 'Large Context';

        if ($this->latency_score < 0.5) $tags[] = 'Fast';

        $ratio = $this->input_cost_per_m > 0 ? $this->output_cost_per_m / $this->input_cost_per_m : 99;
        if ($ratio <= 2.5) $tags[] = 'Efficient Output';

        return $tags;
    }

    public function isRecentlyUpdated(): bool
    {
        return $this->last_updated && $this->last_updated->diffInDays(now()) <= 7;
    }

    public function monthlyEstimate(int $tokens = 5000000): float
    {
        return ($tokens * 0.7 / 1_000_000 * $this->input_cost_per_m)
             + ($tokens * 0.3 / 1_000_000 * $this->output_cost_per_m);
    }

    public function scopeByCategory($query, ?string $category)
    {
        return $category ? $query->where('category', $category) : $query;
    }

    public function categoryLabel(): string
    {
        return match($this->category) {
            'text' => 'Text Generation',
            'image' => 'Image Generation',
            'video' => 'Video Generation',
            'audio' => 'Speech & Audio',
            'embedding' => 'Embeddings',
            'open-source' => 'Open Source',
            default => ucfirst($this->category),
        };
    }
}
