<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'email', 'company', 'monthly_tokens',
        'use_case', 'preferred_providers', 'status', 'notes',
    ];

    protected $casts = [
        'preferred_providers' => 'array',
        'monthly_tokens' => 'integer',
    ];
}
