<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'slug', 'affiliate_url'];

    public function apiModels()
    {
        return $this->hasMany(ApiModel::class);
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public function trackedUrl(?string $modelSlug = null, ?string $from = null): string
    {
        $params = [];
        if ($modelSlug) $params['model'] = $modelSlug;
        if ($from) $params['from'] = $from;
        $qs = $params ? '?' . http_build_query($params) : '';
        return url("/out/{$this->slug}{$qs}");
    }
}
