<?php

namespace App\Http\Controllers;

use App\Models\ApiModel;

class ModelController extends Controller
{
    public function show(string $slug)
    {
        $model = ApiModel::where('slug', $slug)->with('provider')->firstOrFail();

        return view('model.show', [
            'model' => $model,
        ]);
    }

    public function index()
    {
        $models = ApiModel::with('provider')->orderBy('name')->get();

        return view('model.index', [
            'models' => $models,
        ]);
    }
}
