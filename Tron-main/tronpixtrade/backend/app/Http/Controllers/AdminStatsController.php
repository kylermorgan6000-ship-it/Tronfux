<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\CopiedTrade;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;

class AdminStatsController extends ApiController
{
    /**
     * Get aggregated statistics (admin only)
     */
    public function index(): JsonResponse
    {
        try {
            if (!auth()->user()->is_admin) {
                return $this->forbidden('Only admins can view statistics');
            }

            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'total_transactions' => Transaction::count(),
                'transaction_volume' => Transaction::sum('amount') ?? 0,
                'total_trades' => CopiedTrade::count(),
                'active_trades' => CopiedTrade::where('status', 'active')->count(),
                'total_withdrawals' => Withdrawal::count(),
                'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
                'approved_withdrawals' => Withdrawal::where('status', 'approved')->count(),
                'total_withdrawal_amount' => Withdrawal::sum('amount') ?? 0,
                'total_platform_balance' => $this->calculateTotalPlatformBalance(),
                'total_fees_collected' => $this->calculateTotalFeesCollected(),
                'user_growth' => $this->getUserGrowthStats(),
                'transaction_by_type' => $this->getTransactionsByType(),
                'withdrawal_by_status' => $this->getWithdrawalsByStatus(),
            ];

            return $this->success(['statistics' => $stats], 'Admin statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve statistics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Calculate total platform balance across all wallets
     */
    private function calculateTotalPlatformBalance(): float
    {
        // Assuming there's a relation to wallets through users
        $total = 0;
        $users = User::all();
        foreach ($users as $user) {
            if (method_exists($user, 'wallets')) {
                $total += $user->wallets()->sum('balance') ?? 0;
            }
        }
        return $total;
    }

    /**
     * Calculate total fees collected
     */
    private function calculateTotalFeesCollected(): float
    {
        return Transaction::where('type', 'fee')->sum('amount') ?? 0;
    }

    /**
     * Get user growth statistics
     */
    private function getUserGrowthStats(): array
    {
        return [
            'this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'last_month' => User::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count(),
            'this_year' => User::whereYear('created_at', now()->year)->count(),
        ];
    }

    /**
     * Get transactions by type
     */
    private function getTransactionsByType(): array
    {
        return Transaction::selectRaw('type, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('type')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->type,
                    'count' => $item->count,
                    'total' => $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get withdrawals by status
     */
    private function getWithdrawalsByStatus(): array
    {
        return Withdrawal::selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status,
                    'count' => $item->count,
                    'total' => $item->total,
                ];
            })
            ->toArray();
    }
}
