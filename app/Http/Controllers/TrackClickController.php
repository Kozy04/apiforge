<?php

namespace App\Http\Controllers;

use App\Models\ApiModel;
use App\Models\Click;
use App\Models\Provider;

class TrackClickController extends Controller
{
    public function __invoke(string $providerSlug)
    {
        $provider = Provider::where('slug', $providerSlug)->firstOrFail();

        $modelId = null;
        if ($modelSlug = request()->query('model')) {
            $model = ApiModel::where('slug', $modelSlug)->first();
            $modelId = $model?->id;
        }

        Click::create([
            'provider_id'  => $provider->id,
            'api_model_id' => $modelId,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'referrer'     => request()->header('referer'),
            'page'         => request()->query('from'),
        ]);

        return redirect()->away($provider->affiliate_url);
    }
}
