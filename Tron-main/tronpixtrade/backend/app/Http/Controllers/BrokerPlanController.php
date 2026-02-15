<?php

namespace App\Http\Controllers;

use App\Models\BrokerPlan;
use Illuminate\Http\Request;

class BrokerPlanController extends Controller
{
    public function index()
    {
        return BrokerPlan::all();
    }

    public function show($id)
    {
        return BrokerPlan::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_investment' => 'required|numeric',
            'max_investment' => 'required|numeric',
            'roi_percentage' => 'required|numeric',
            'duration_days' => 'required|integer',
            'status' => 'string|in:active,inactive',
        ]);

        $plan = BrokerPlan::create($request->all());
        return response()->json($plan, 201);
    }

    public function update(Request $request, $id)
    {
        $plan = BrokerPlan::findOrFail($id);

        $request->validate([
            'name' => 'string|max:255',
            'min_investment' => 'numeric',
            'max_investment' => 'numeric',
            'roi_percentage' => 'numeric',
            'duration_days' => 'integer',
            'status' => 'string|in:active,inactive',
        ]);

        $plan->update($request->all());
        return response()->json($plan);
    }

    public function destroy($id)
    {
        $plan = BrokerPlan::findOrFail($id);
        $plan->delete();
        return response()->json(['message' => 'Plan deleted']);
    }
}