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

    public function index(?string $category = null)
    {
        $query = ApiModel::byCategory($category)->with('provider');
        $models = $query->orderBy('name')->get();

        $categories = ApiModel::select('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        return view('model.index', [
            'models' => $models,
            'activeCategory' => $category,
            'categories' => $categories,
        ]);
    }
}
