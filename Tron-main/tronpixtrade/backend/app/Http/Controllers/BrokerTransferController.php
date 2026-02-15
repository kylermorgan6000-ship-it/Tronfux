<?php

namespace App\Http\Controllers;

use App\Models\BrokerTransfer;
use Illuminate\Http\Request;

class BrokerTransferController extends Controller
{
    public function index()
    {
        return BrokerTransfer::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:broker_users,id',
            'amount' => 'required|numeric',
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'routing_number' => 'string|nullable',
            'bank_address' => 'string|nullable',
            'transaction_pin' => 'required|string|size:4',
        ]);

        $transfer = BrokerTransfer::create([
            'broker_user_id' => $request->wallet_id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'routing_number' => $request->routing_number,
            'bank_address' => $request->bank_address,
            'status' => 'pending'
        ]);

        return response()->json($transfer, 201);
    }

    public function show($id)
    {
        return BrokerTransfer::findOrFail($id);
    }
}
