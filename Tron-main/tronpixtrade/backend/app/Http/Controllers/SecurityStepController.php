<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifySecurityStepRequest;
use App\Models\SecurityStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecurityStepController extends ApiController
{
    /**
     * List security steps for authenticated user or admin by type
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SecurityStep::query();

            if (auth()->user()->is_admin) {
                // Admin can filter by type
                if ($request->has('type')) {
                    $query->where('type', $request->type);
                }
            } else {
                // Regular users see only their own
                $query->where('user_id', auth()->id());
            }

            $steps = $query->latest()->paginate(15);

            return $this->successWithPagination($steps, 'Security steps retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve security steps: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify security code
     */
    public function verify(VerifySecurityStepRequest $request, $id): JsonResponse
    {
        try {
            $step = SecurityStep::find($id);

            if (!$step) {
                return $this->notFound('Security step not found');
            }

            // Check authorization
            if ($step->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this security step');
            }

            // Verify the code
            if ($step->code === $request->code) {
                $step->update(['is_verified' => true, 'verified_at' => now()]);
                return $this->success(['step' => $step], 'Security step verified successfully');
            }

            return $this->error('Invalid verification code', 422);
        } catch (\Exception $e) {
            return $this->error('Failed to verify security step: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a new security step (admin)
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'explanation' => 'required|string',
            'code' => 'required|string',
            'account' => 'required|string',
            'type' => 'required|in:bank,broker',
        ]);

        try {
            $step = SecurityStep::create($request->all());
            return $this->success(['step' => $step], 'Security step created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create security step: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update security step (admin)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'explanation' => 'required|string',
            'code' => 'required|string',
            'account' => 'required|string',
            'type' => 'required|in:bank,broker',
        ]);

        try {
            $step = SecurityStep::find($id);
            if (!$step) {
                return $this->notFound('Security step not found');
            }

            $step->update($request->all());
            return $this->success(['step' => $step], 'Security step updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update security step: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete security step (admin)
     */
    public function destroy($id): JsonResponse
    {
        try {
            $step = SecurityStep::find($id);
            if (!$step) {
                return $this->notFound('Security step not found');
            }

            $step->delete();
            return $this->success([], 'Security step deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete security step: ' . $e->getMessage(), 500);
        }
    }
}
