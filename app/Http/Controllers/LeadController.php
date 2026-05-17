<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'monthly_tokens' => 'nullable|integer|min:0',
            'use_case' => 'nullable|string|max:500',
            'preferred_providers' => 'nullable|array',
            'preferred_providers.*' => 'string',
        ]);

        Lead::create($validated + ['status' => 'new']);

        return response()->json([
            'message' => 'Quote request received. We will match you with the best providers.',
        ], 201);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        \App\Models\Subscriber::firstOrCreate(['email' => $request->email]);

        return response()->json([
            'message' => 'Subscribed to price alerts.',
        ]);
    }
}
