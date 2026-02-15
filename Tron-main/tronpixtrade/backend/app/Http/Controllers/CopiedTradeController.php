<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCopiedTradeRequest;
use App\Http\Requests\UpdateCopiedTradeRequest;
use App\Models\CopiedTrade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CopiedTradeController extends ApiController
{
    /**
     * List user copied trades
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = CopiedTrade::where('user_id', auth()->id())
                ->with('traderProfile', 'traderProfile.user');

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('trader_profile_id')) {
                $query->where('trader_profile_id', $request->trader_profile_id);
            }

            $trades = $query->latest()->paginate($request->per_page ?? 15);

            return $this->successWithPagination($trades, 'Copied trades retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve copied trades: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Start copying trader
     */
    public function store(StoreCopiedTradeRequest $request): JsonResponse
    {
        try {
            // Check if user is already copying this trader
            $existing = CopiedTrade::where('user_id', auth()->id())
                ->where('trader_profile_id', $request->trader_profile_id)
                ->whereIn('status', ['active', 'paused'])
                ->first();

            if ($existing) {
                return $this->error('You are already copying this trader', 422);
            }

            $copiedTrade = CopiedTrade::create([
                'user_id' => auth()->id(),
                'trader_profile_id' => $request->trader_profile_id,
                'initial_investment' => $request->initial_investment,
                'current_balance' => $request->initial_investment,
                'max_positions' => $request->max_positions ?? 5,
                'stop_loss_percentage' => $request->stop_loss_percentage ?? 10,
                'status' => 'active',
            ]);

            return $this->success(['copied_trade' => $copiedTrade], 'Started copying trader successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to start copying trader: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get copied trade details
     */
    public function show($id): JsonResponse
    {
        try {
            $copiedTrade = CopiedTrade::with('traderProfile', 'traderProfile.user')->find($id);

            if (!$copiedTrade) {
                return $this->notFound('Copied trade not found');
            }

            // Check authorization
            if ($copiedTrade->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this copied trade');
            }

            return $this->success(['copied_trade' => $copiedTrade], 'Copied trade retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve copied trade: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update settings (max_positions, stop_loss)
     */
    public function update(UpdateCopiedTradeRequest $request, $id): JsonResponse
    {
        try {
            $copiedTrade = CopiedTrade::find($id);

            if (!$copiedTrade) {
                return $this->notFound('Copied trade not found');
            }

            // Check authorization
            if ($copiedTrade->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have permission to update this copied trade');
            }

            $copiedTrade->update($request->only(['max_positions', 'stop_loss_percentage']));

            return $this->success(['copied_trade' => $copiedTrade], 'Copied trade updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update copied trade: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Stop copying trader
     */
    public function destroy($id): JsonResponse
    {
        try {
            $copiedTrade = CopiedTrade::find($id);

            if (!$copiedTrade) {
                return $this->notFound('Copied trade not found');
            }

            // Check authorization
            if ($copiedTrade->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have permission to stop this copied trade');
            }

            $copiedTrade->update(['status' => 'stopped']);

            return $this->success(['copied_trade' => $copiedTrade], 'Stopped copying trader successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to stop copying trader: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Pause/Resume copied trade
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $copiedTrade = CopiedTrade::find($id);

            if (!$copiedTrade) {
                return $this->notFound('Copied trade not found');
            }

            // Check authorization
            if ($copiedTrade->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have permission to toggle this copied trade');
            }

            $newStatus = $copiedTrade->status === 'active' ? 'paused' : 'active';
            $copiedTrade->update(['status' => $newStatus]);

            return $this->success(['copied_trade' => $copiedTrade], 'Copied trade status updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to toggle copied trade status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * List all copied trades (admin)
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = CopiedTrade::with('user', 'traderProfile', 'traderProfile.user');

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $trades = $query->latest()->paginate($request->per_page ?? 15);

            return $this->successWithPagination($trades, 'Copied trades retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve copied trades: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Close a copied trade
     */
    public function close($id): JsonResponse
    {
        try {
            $copiedTrade = CopiedTrade::find($id);

            if (!$copiedTrade) {
                return $this->notFound('Copied trade not found');
            }

            // Check authorization
            if ($copiedTrade->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have permission to close this copied trade');
            }

            $copiedTrade->update(['status' => 'closed']);

            return $this->success(['copied_trade' => $copiedTrade], 'Copied trade closed successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to close copied trade: ' . $e->getMessage(), 500);
        }
    }
}
