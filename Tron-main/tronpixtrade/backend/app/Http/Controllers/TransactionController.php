<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends ApiController
{
    /**
     * List transactions with filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Transaction::where('user_id', auth()->id());

            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
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

            // Sorting
            if ($request->has('sort')) {
                $direction = $request->has('direction') && $request->direction === 'asc' ? 'asc' : 'desc';
                $query->orderBy($request->sort, $direction);
            } else {
                $query->latest();
            }

            $transactions = $query->paginate($request->per_page ?? 15);

            return $this->successWithPagination($transactions, 'Transactions retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve transactions: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get transaction details
     */
    public function show($id): JsonResponse
    {
        try {
            $transaction = Transaction::find($id);

            if (!$transaction) {
                return $this->notFound('Transaction not found');
            }

            // Check authorization
            if ($transaction->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this transaction');
            }

            return $this->success(['transaction' => $transaction], 'Transaction retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve transaction: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get transaction statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $userId = auth()->id();

            $stats = [
                'total_transactions' => Transaction::where('user_id', $userId)->count(),
                'by_type' => Transaction::where('user_id', $userId)
                    ->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get(),
                'by_status' => Transaction::where('user_id', $userId)
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->get(),
                'total_income' => Transaction::where('user_id', $userId)
                    ->where('type', 'income')
                    ->sum('amount'),
                'total_expense' => Transaction::where('user_id', $userId)
                    ->where('type', 'expense')
                    ->sum('amount'),
            ];

            return $this->success(['statistics' => $stats], 'Transaction statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve statistics: ' . $e->getMessage(), 500);
        }
    }
}
