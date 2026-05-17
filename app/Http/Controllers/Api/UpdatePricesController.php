<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiModel;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UpdatePricesController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'models' => 'required|array',
            'models.*.provider_slug' => 'required|string',
            'models.*.name' => 'required|string',
            'models.*.slug' => 'required|string',
            'models.*.input_cost_per_m' => 'nullable|numeric',
            'models.*.output_cost_per_m' => 'nullable|numeric',
            'models.*.context_window' => 'nullable|integer',
            'models.*.latency_score' => 'nullable|numeric',
        ]);

        $providers = Provider::all()->keyBy('slug');
        $updated = 0;
        $created = 0;

        foreach ($request->input('models') as $data) {
            $provider = $providers->get($data['provider_slug']);
            if (!$provider) continue;

            $model = ApiModel::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'provider_id'      => $provider->id,
                    'name'             => $data['name'],
                    'input_cost_per_m' => $data['input_cost_per_m'] ?? 0,
                    'output_cost_per_m'=> $data['output_cost_per_m'] ?? 0,
                    'context_window'   => $data['context_window'] ?? 0,
                    'latency_score'    => $data['latency_score'] ?? 0,
                    'last_updated'     => Carbon::now(),
                ]
            );

            if ($model->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        return response()->json([
            'message' => 'Prices updated successfully.',
            'updated' => $updated,
            'created' => $created,
        ]);
    }
}
