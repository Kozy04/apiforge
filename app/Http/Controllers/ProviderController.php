<?php

namespace App\Http\Controllers;

use App\Models\Provider;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = Provider::withCount('apiModels')
            ->withCount('clicks')
            ->orderByDesc('clicks_count')
            ->get();

        return view('provider.index', ['providers' => $providers]);
    }

    public function show(string $slug)
    {
        $provider = Provider::where('slug', $slug)
            ->with(['apiModels' => fn($q) => $q->orderBy('input_cost_per_m')])
            ->firstOrFail();

        return view('provider.show', ['provider' => $provider]);
    }
}
