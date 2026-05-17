<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['email', 'confirmed_at'];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];
}
