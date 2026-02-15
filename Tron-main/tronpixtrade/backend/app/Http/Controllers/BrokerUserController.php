<?php

namespace App\Http\Controllers;

use App\Models\BrokerUser;
use App\Models\BrokerTransaction;
use App\Mail\AdminCreditsUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class BrokerUserController extends Controller
{
    public function index() {
        return BrokerUser::all();
    }

    public function approveKyc($id) {
        $user = BrokerUser::findOrFail($id);
        $user->status = 'active';
        $user->save();
        return response()->json($user);
    }

    public function rejectKyc($id) {
        $user = BrokerUser::findOrFail($id);
        $user->status = 'rejected';
        $user->save();
        return response()->json($user);
    }

    public function deleteKyc($id) {
        $user = BrokerUser::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Broker KYC deleted']);
    }

    public function credit(Request $request) {
        $request->validate(['email' => 'required|email', 'amount' => 'required|numeric|min:0.01']);
        $user = BrokerUser::where('email', $request->email)->firstOrFail();
        
        $oldBalance = $user->balance;
        $user->balance += $request->amount;
        $user->save();

        // Create transaction record
        $transaction = BrokerTransaction::create([
            'broker_user_id' => $user->id,
            'type' => 'credit_admin',
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'USD',
            'status' => 'completed',
        ]);

        // Create a Transaction-like object for the email (since BrokerTransaction is different)
        $transaction->description = 'Admin credit';
        $transaction->user = $user;

        // Send notification email
        try {
            // Since we don't have a separate email model for broker, we'll send a custom notification
            Mail::to($user->email)->send(new AdminCreditsUser($user, $transaction, 'broker'));
        } catch (\Exception $e) {
            // Log error but don't fail the credit operation
            \Log::error('Failed to send broker credit email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Broker user credited successfully',
            'user' => $user,
            'transaction' => $transaction,
        ]);
    }

    public function deduct(Request $request) {
        $request->validate(['email' => 'required|email', 'amount' => 'required|numeric|min:0.01']);
        $user = BrokerUser::where('email', $request->email)->firstOrFail();
        
        if ($user->balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance'], 422);
        }

        $user->balance -= $request->amount;
        $user->save();

        // Create transaction record
        $transaction = BrokerTransaction::create([
            'broker_user_id' => $user->id,
            'type' => 'debit_admin',
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'USD',
            'status' => 'completed',
        ]);

        return response()->json([
            'message' => 'Broker user debited successfully',
            'user' => $user,
            'transaction' => $transaction,
        ]);
    }
}