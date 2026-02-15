<?php

namespace App\Http\Controllers;

use App\Models\BrokerTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrokerTransactionController extends ApiController
{
    /**
     * List broker transactions
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = BrokerTransaction::query();

            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $transactions = $query->paginate($request->per_page ?? 15);

            return $this->successWithPagination($transactions, 'Broker transactions retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve broker transactions: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get broker transaction details
     */
    public function show($id): JsonResponse
    {
        try {
            $transaction = BrokerTransaction::find($id);

            if (!$transaction) {
                return $this->notFound('Broker transaction not found');
            }

            return $this->success(['transaction' => $transaction], 'Broker transaction retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve broker transaction: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create broker transaction
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'broker_user_id' => 'required|exists:broker_users,id',
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'string|max:10',
            'status' => 'string|in:pending,completed,failed',
        ]);

        try {
            $transaction = BrokerTransaction::create($request->all());

            return $this->success(['transaction' => $transaction], 'Broker transaction created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create broker transaction: ' . $e->getMessage(), 500);
        }
    }
}