<?php

namespace App\Http\Controllers;

use App\Models\ApiModel;

class CompareController extends Controller
{
    public function index(string $modelOneSlug, string $modelTwoSlug)
    {
        $modelOne = ApiModel::where('slug', $modelOneSlug)->with('provider')->firstOrFail();
        $modelTwo = ApiModel::where('slug', $modelTwoSlug)->with('provider')->firstOrFail();

        $cheaperInput = $modelOne->input_cost_per_m <= $modelTwo->input_cost_per_m ? $modelOne : $modelTwo;
        $cheaperOutput = $modelOne->output_cost_per_m <= $modelTwo->output_cost_per_m ? $modelOne : $modelTwo;

        return view('compare.index', [
            'modelOne' => $modelOne,
            'modelTwo' => $modelTwo,
            'cheaperInput' => $cheaperInput,
            'cheaperOutput' => $cheaperOutput,
        ]);
    }
}
