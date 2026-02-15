<?php

namespace App\Http\Controllers;

use App\Models\BrokerWithdrawal;
use App\Models\BrokerUser;
use Illuminate\Http\Request;

class BrokerWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = BrokerWithdrawal::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:broker_users,id',
            'amount' => 'required|numeric',
            'currency' => 'required|string|max:10',
            'withdrawal_method' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'pin' => 'required|string|size:4'
        ]);

        $user = BrokerUser::findOrFail($request->wallet_id);

        if ($user->pin !== $request->pin) {
            return response()->json(['message' => 'Invalid PIN'], 403);
        }

        if ($user->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 403);
        }

        $user->balance -= $request->amount;
        $user->save();

        $withdrawal = BrokerWithdrawal::create([
            'broker_user_id' => $request->wallet_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'withdrawal_method' => $request->withdrawal_method,
            'destination' => $request->destination,
            'status' => 'pending'
        ]);

        return response()->json($withdrawal, 201);
    }

    public function show($id)
    {
        return BrokerWithdrawal::findOrFail($id);
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:4',
            'wallet_id' => 'required|exists:broker_users,id'
        ]);

        $user = BrokerUser::findOrFail($request->wallet_id);
        $verified = $user->pin === $request->pin;

        return response()->json(['verified' => $verified]);
    }
}