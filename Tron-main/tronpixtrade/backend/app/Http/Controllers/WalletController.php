<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends ApiController
{
    /**
     * List user wallets (paginated)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Wallet::where('user_id', auth()->id());

            if ($request->has('currency')) {
                $query->where('currency', $request->currency);
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $wallets = $query->paginate($request->per_page ?? 15);

            return $this->successWithPagination($wallets, 'Wallets retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve wallets: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create wallet
     */
    public function store(StoreWalletRequest $request): JsonResponse
    {
        try {
            $wallet = Wallet::create([
                'user_id' => auth()->id(),
                'currency' => $request->currency,
                'balance' => $request->initial_balance ?? 0,
                'is_active' => true,
            ]);

            return $this->success(['wallet' => $wallet], 'Wallet created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create wallet: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get wallet details
     */
    public function show($id): JsonResponse
    {
        try {
            $wallet = Wallet::find($id);

            if (!$wallet) {
                return $this->notFound('Wallet not found');
            }

            // Check authorization
            if ($wallet->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this wallet');
            }

            return $this->success(['wallet' => $wallet], 'Wallet retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve wallet: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get wallet balance
     */
    public function balance($id): JsonResponse
    {
        try {
            $wallet = Wallet::find($id);

            if (!$wallet) {
                return $this->notFound('Wallet not found');
            }

            // Check authorization
            if ($wallet->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this wallet');
            }

            return $this->success(['balance' => $wallet->balance], 'Wallet balance retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve wallet balance: ' . $e->getMessage(), 500);
        }
    }
