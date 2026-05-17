<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    protected $fillable = [
        'provider_id', 'api_model_id', 'ip_address',
        'user_agent', 'referrer', 'page',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function apiModel()
    {
        return $this->belongsTo(ApiModel::class);
    }
}
