<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiModel extends Model
{
    protected $fillable = [
        'provider_id', 'name', 'slug',
        'input_cost_per_m', 'output_cost_per_m',
        'context_window', 'latency_score', 'last_updated',
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
}
