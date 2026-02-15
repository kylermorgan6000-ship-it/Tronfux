<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAccountController extends ApiController
{
    /**
     * List user bank accounts
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = BankAccount::where('user_id', auth()->id());

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $accounts = $query->paginate($request->per_page ?? 15);

            return $this->successWithPagination($accounts, 'Bank accounts retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve bank accounts: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create bank account
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'account_name' => 'required|string',
            'account_type' => 'required|in:checking,savings,investment',
            'currency' => 'required|string',
        ]);

        try {
            $account = BankAccount::create([
                'user_id' => auth()->id(),
                'account_name' => $request->account_name,
                'account_type' => $request->account_type,
                'currency' => $request->currency,
                'status' => 'active',
            ]);

            return $this->success(['account' => $account], 'Bank account created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create bank account: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get bank account details
     */
    public function show($id): JsonResponse
    {
        try {
            $account = BankAccount::find($id);

            if (!$account) {
                return $this->notFound('Bank account not found');
            }

            // Check authorization
            if ($account->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this bank account');
            }

            return $this->success(['account' => $account], 'Bank account retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve bank account: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update bank account
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $account = BankAccount::find($id);

            if (!$account) {
                return $this->notFound('Bank account not found');
            }

            // Check authorization
            if ($account->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have permission to update this bank account');
            }

            $account->update($request->only(['account_name', 'account_type', 'currency', 'status']));

            return $this->success(['account' => $account], 'Bank account updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update bank account: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete bank account
     */
    public function destroy($id): JsonResponse
    {
        try {
            $account = BankAccount::find($id);

            if (!$account) {
                return $this->notFound('Bank account not found');
            }

            // Check authorization
            if ($account->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have permission to delete this bank account');
            }

            $account->delete();

            return $this->success(null, 'Bank account deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete bank account: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get bank account balance
     */
    public function getBalance($id): JsonResponse
    {
        try {
            $account = BankAccount::find($id);

            if (!$account) {
                return $this->notFound('Bank account not found');
            }

            // Check authorization
            if ($account->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this bank account');
            }

            return $this->success(['balance' => $account->balance], 'Bank account balance retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve bank account balance: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get transaction history
     */
    public function getTransactionHistory($id, Request $request): JsonResponse
    {
        try {
            $account = BankAccount::find($id);

            if (!$account) {
                return $this->notFound('Bank account not found');
            }

            // Check authorization
            if ($account->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this bank account');
            }

            $transactions = $account->transactions()->paginate($request->per_page ?? 20);

            return $this->successWithPagination($transactions, 'Transaction history retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve transaction history: ' . $e->getMessage(), 500);
        }
    }
}