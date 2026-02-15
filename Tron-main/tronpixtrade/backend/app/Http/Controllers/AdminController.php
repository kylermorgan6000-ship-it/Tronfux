<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\BrokerTransaction;
use App\Mail\AdminCreditsUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function statistics()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $totalBalance = Wallet::sum('balance');
        $brokerTrades = BrokerTransaction::count();

        return response()->json([
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_balance' => $totalBalance,
            'broker_trades' => $brokerTrades,
        ]);
    }

    public function users(Request $request)
    {
        $type = $request->get('type');
        $query = User::query();

        if ($type) {
            $query->where('account_type', $type);
        }

        $users = $query->paginate(20);
        return response()->json($users);
    }

    public function kyc(Request $request)
    {
        $type = $request->get('type', 'bank');
        $query = User::where('account_type', $type);

        $users = $query->with('kycSubmissions')->paginate(20);
        return response()->json($users);
    }

    public function bankUsers()
    {
        $users = User::where('account_type', 'banking')->paginate(20);
        return response()->json($users);
    }

    public function brokerTrades()
    {
        $trades = BrokerTransaction::paginate(20);
        return response()->json($trades);
    }

    public function wallets()
    {
        $wallets = Wallet::with('user')->paginate(20);
        return response()->json($wallets);
    }

    public function creditUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $wallet = $user->wallets()->first();
        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        // Update wallet balance
        $wallet->increment('balance', $request->amount);

        // Create transaction record
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'credit_admin',
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status' => 'completed',
            'description' => 'Admin credit',
            'metadata' => [
                'credited_by' => auth()->id(),
                'credited_by_email' => auth()->user()->email,
            ],
        ]);

        // Send notification email
        try {
            Mail::to($user->email)->send(new AdminCreditsUser($user, $transaction, $user->account_type));
        } catch (\Exception $e) {
            // Log error but don't fail the credit operation
            \Log::error('Failed to send admin credit email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'User credited successfully',
            'transaction' => $transaction,
            'wallet' => $wallet,
        ]);
    }

    public function deductUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $wallet = $user->wallets()->first();
        if (!$wallet || $wallet->balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance'], 422);
        }

        // Update wallet balance
        $wallet->decrement('balance', $request->amount);

        // Create transaction record
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'debit_admin',
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status' => 'completed',
            'description' => 'Admin debit',
            'metadata' => [
                'debited_by' => auth()->id(),
                'debited_by_email' => auth()->user()->email,
            ],
        ]);

        return response()->json([
            'message' => 'User debited successfully',
            'transaction' => $transaction,
            'wallet' => $wallet,
        ]);
    }

    public function approveKyc($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['kyc_status' => 'approved']);
        return response()->json(['message' => 'KYC approved']);
    }

    public function rejectKyc($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['kyc_status' => 'rejected']);
        return response()->json(['message' => 'KYC rejected']);
    }

    public function deleteKyc($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->kycSubmissions()->delete();
        return response()->json(['message' => 'KYC deleted']);
    }

    public function updateUserStatus(Request $request, $userId)
    {
        $request->validate([
            'status' => 'required|string|in:active,pending,suspended',
        ]);

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['status' => $request->status]);
        return response()->json(['message' => 'User status updated']);
    }

    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}