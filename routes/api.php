<?php

use App\Http\Controllers\Api\UpdatePricesController;
use App\Models\ApiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/update-prices', UpdatePricesController::class)
    ->middleware('scraper.key');

Route::post('/blog-posts', [\App\Http\Controllers\BlogController::class, 'webhook'])
    ->middleware('scraper.key');

Route::get('/search', function (Request $request) {
    $q = $request->query('q', '');
    if (mb_strlen($q) < 2) return response()->json([]);

    return ApiModel::with('provider')
        ->where('name', 'like', "%{$q}%")
        ->orWhere('slug', 'like', "%{$q}%")
        ->limit(8)
        ->get()
        ->map(fn ($m) => [
            'slug' => $m->slug,
            'name' => $m->name,
            'provider' => $m->provider->name,
            'input_cost' => $m->input_cost_per_m,
        ]);
});
