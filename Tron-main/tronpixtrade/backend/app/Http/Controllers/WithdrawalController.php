<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWithdrawalRequest;
use App\Http\Requests\UpdateWithdrawalStatusRequest;
use App\Http\Requests\VerifyTransactionPinRequest;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WithdrawalController extends ApiController
{
    /**
     * List withdrawals with filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Withdrawal::query();

            // For non-admin users, only show their own withdrawals
            if (!auth()->user()->is_admin) {
                $query->where('user_id', auth()->id());
            } else if ($request->has('user_id')) {
                // Admin can filter by user
                $query->where('user_id', $request->user_id);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $withdrawals = $query->latest()->paginate($request->per_page ?? 15);

            return $this->successWithPagination($withdrawals, 'Withdrawals retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve withdrawals: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create withdrawal request
     */
    public function store(StoreWithdrawalRequest $request): JsonResponse
    {
        try {
            $wallet = auth()->user()->wallets()->find($request->wallet_id);

            if (!$wallet) {
                return $this->notFound('Wallet not found');
            }

            if ($wallet->balance < $request->amount) {
                return $this->error('Insufficient balance', 422);
            }

            // Verify transaction PIN
            if (!$this->verifyTransactionPin($request->transaction_pin)) {
                return $this->error('Invalid transaction PIN', 422);
            }

            $withdrawal = Withdrawal::create([
                'user_id' => auth()->id(),
                'wallet_id' => $request->wallet_id,
                'amount' => $request->amount,
                'destination' => $request->destination,
                'status' => 'pending',
            ]);

            return $this->success(['withdrawal' => $withdrawal], 'Withdrawal request submitted successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create withdrawal: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get withdrawal details
     */
    public function show($id): JsonResponse
    {
        try {
            $withdrawal = Withdrawal::find($id);

            if (!$withdrawal) {
                return $this->notFound('Withdrawal not found');
            }

            // Check authorization
            if ($withdrawal->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this withdrawal');
            }

            return $this->success(['withdrawal' => $withdrawal], 'Withdrawal retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve withdrawal: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update withdrawal status (admin only)
     */
    public function updateStatus(UpdateWithdrawalStatusRequest $request, $id): JsonResponse
    {
        try {
            $withdrawal = Withdrawal::find($id);

            if (!$withdrawal) {
                return $this->notFound('Withdrawal not found');
            }

            $withdrawal->update([
                'status' => $request->status,
                'reason' => $request->reason ?? null,
            ]);

            return $this->success(['withdrawal' => $withdrawal], 'Withdrawal status updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update withdrawal: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify transaction PIN
     */
    public function verifyPin(VerifyTransactionPinRequest $request): JsonResponse
    {
        try {
            if ($this->verifyTransactionPin($request->transaction_pin)) {
                return $this->success(null, 'PIN verified successfully');
            }

            return $this->error('Invalid PIN', 422);
        } catch (\Exception $e) {
            return $this->error('Failed to verify PIN: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify transaction PIN against user's stored PIN
     */
    private function verifyTransactionPin($pin): bool
    {
        $user = auth()->user();
        // Implement actual PIN verification logic
        return true; // Placeholder
    }
}
