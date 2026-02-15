<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestmentPlanRequest;
use App\Http\Requests\UpdateInvestmentPlanRequest;
use App\Models\InvestmentPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestmentPlanController extends ApiController
{
    /**
     * List investment plans with filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = InvestmentPlan::query();

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('min_amount')) {
                $query->where('min_amount', '<=', $request->min_amount);
            }

            if ($request->has('max_amount')) {
                $query->where('max_amount', '>=', $request->max_amount);
            }

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            }

            // Sorting
            if ($request->has('sort')) {
                $direction = $request->has('direction') && $request->direction === 'asc' ? 'asc' : 'desc';
                $query->orderBy($request->sort, $direction);
            } else {
                $query->latest();
            }

            $plans = $query->paginate($request->per_page ?? 15);

            return $this->successWithPagination($plans, 'Investment plans retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve investment plans: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create investment plan (admin only)
     */
    public function store(StoreInvestmentPlanRequest $request): JsonResponse
    {
        try {
            if (!auth()->user()->is_admin) {
                return $this->forbidden('Only admins can create investment plans');
            }

            $plan = InvestmentPlan::create([
                'name' => $request->name,
                'description' => $request->description,
                'min_amount' => $request->min_amount,
                'max_amount' => $request->max_amount,
                'duration_days' => $request->duration_days,
                'return_percentage' => $request->return_percentage,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return $this->success(['plan' => $plan], 'Investment plan created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create investment plan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get investment plan details
     */
    public function show($id): JsonResponse
    {
        try {
            $plan = InvestmentPlan::find($id);

            if (!$plan) {
                return $this->notFound('Investment plan not found');
            }

            return $this->success(['plan' => $plan], 'Investment plan retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve investment plan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update investment plan (admin only)
     */
    public function update(UpdateInvestmentPlanRequest $request, $id): JsonResponse
    {
        try {
            if (!auth()->user()->is_admin) {
                return $this->forbidden('Only admins can update investment plans');
            }

            $plan = InvestmentPlan::find($id);

            if (!$plan) {
                return $this->notFound('Investment plan not found');
            }

            $plan->update($request->only([
                'name',
                'description',
                'min_amount',
                'max_amount',
                'duration_days',
                'return_percentage',
                'is_active',
            ]));

            return $this->success(['plan' => $plan], 'Investment plan updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update investment plan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete investment plan (admin only)
     */
    public function destroy($id): JsonResponse
    {
        try {
            if (!auth()->user()->is_admin) {
                return $this->forbidden('Only admins can delete investment plans');
            }

            $plan = InvestmentPlan::find($id);

            if (!$plan) {
                return $this->notFound('Investment plan not found');
            }

            $plan->delete();

            return $this->success(null, 'Investment plan deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete investment plan: ' . $e->getMessage(), 500);
        }
    }
}
