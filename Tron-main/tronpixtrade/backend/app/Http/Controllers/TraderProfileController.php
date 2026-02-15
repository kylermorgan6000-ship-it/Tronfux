<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTraderProfileRequest;
use App\Models\TraderProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TraderProfileController extends ApiController
{
    /**
     * List trader profiles with filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = TraderProfile::query();

            // Filter by risk level
            if ($request->has('risk_level')) {
                $query->where('risk_level', $request->risk_level);
            }

            // Filter by minimum investment
            if ($request->has('min_investment')) {
                $query->where('min_investment', '<=', $request->min_investment);
            }

            // Search by name/description
            if ($request->has('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('description', 'like', '%' . $request->search . '%')
                        ->orWhereHas('user', function ($u) use ($request) {
                            $u->where('name', 'like', '%' . $request->search . '%');
                        });
                });
            }

            // Sorting
            if ($request->has('sort')) {
                $direction = $request->has('direction') && $request->direction === 'asc' ? 'asc' : 'desc';
                $query->orderBy($request->sort, $direction);
            } else {
                $query->latest();
            }

            $profiles = $query->paginate($request->per_page ?? 15);

            return $this->successWithPagination($profiles, 'Trader profiles retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve trader profiles: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get trader details
     */
    public function show($id): JsonResponse
    {
        try {
            $profile = TraderProfile::with('user')->find($id);

            if (!$profile) {
                return $this->notFound('Trader profile not found');
            }

            return $this->success(['profile' => $profile], 'Trader profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve trader profile: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get trader performance statistics
     */
    public function statistics($id): JsonResponse
    {
        try {
            $profile = TraderProfile::find($id);

            if (!$profile) {
                return $this->notFound('Trader profile not found');
            }

            $stats = [
                'total_followers' => $profile->copiedTrades()->count(),
                'total_return_percentage' => $profile->return_percentage,
                'total_trades' => $profile->totalTrades ?? 0,
                'winning_trades' => $profile->winningTrades ?? 0,
                'win_rate' => $profile->totalTrades > 0 
                    ? ($profile->winningTrades / $profile->totalTrades) * 100 
                    : 0,
                'average_trade_duration' => $profile->averageTradeDuration ?? 0,
                'monthly_return' => $profile->monthlyReturn ?? 0,
                'risk_reward_ratio' => $profile->riskRewardRatio ?? 0,
            ];

            return $this->success(['statistics' => $stats], 'Trader statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve trader statistics: ' . $e->getMessage(), 500);
        }
    }
}
