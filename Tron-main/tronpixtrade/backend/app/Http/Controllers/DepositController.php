<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepositController extends ApiController
{
    /**
     * List deposits
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Transaction::where('user_id', auth()->id())
                ->where('type', 'deposit');

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $deposits = $query->paginate($request->per_page ?? 15);

            return $this->successWithPagination($deposits, 'Deposits retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve deposits: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create deposit request
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string',
            'payment_method' => 'required|in:card,bank_transfer,crypto,paypal',
        ]);

        try {
            $wallet = auth()->user()->wallets()->find($request->wallet_id);

            if (!$wallet) {
                return $this->notFound('Wallet not found');
            }

            $deposit = Transaction::create([
                'user_id' => auth()->id(),
                'wallet_id' => $request->wallet_id,
                'type' => 'deposit',
                'amount' => $request->amount,
                'currency' => $request->currency,
                'status' => 'pending',
                'description' => 'Deposit via ' . $request->payment_method,
                'metadata' => ['payment_method' => $request->payment_method],
            ]);

            return $this->success(['deposit' => $deposit], 'Deposit request submitted successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create deposit: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get deposit details
     */
    public function show($id): JsonResponse
    {
        try {
            $deposit = Transaction::where('id', $id)
                ->where('type', 'deposit')
                ->first();

            if (!$deposit) {
                return $this->notFound('Deposit not found');
            }

            // Check authorization
            if ($deposit->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this deposit');
            }

            return $this->success(['deposit' => $deposit], 'Deposit retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve deposit: ' . $e->getMessage(), 500);
        }
    }
}