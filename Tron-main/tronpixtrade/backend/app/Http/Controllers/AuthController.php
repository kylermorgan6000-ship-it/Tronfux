<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends ApiController
{
    public function register(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'account_type' => ['required', Rule::in(['broker','banking'])],
    ]);

    // Create the user
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'account_type' => $data['account_type'],
        'status' => 'active',
    ]);

    // If the user is a banking user, create a default bank account
    if ($data['account_type'] === 'banking') {
        BankAccount::create([
            'user_id' => $user->id,           // link to the newly created user
            'account_name' => 'Main Account', // default name
            'account_type' => 'savings',      // default type
            'balance' => 0.00,              // initial balance
            'currency' => 'USD',              // default currency
            'status' => 'active',             // account status
        ]);
    }

    // Authenticate the user
    Auth::login($user);

    return $this->success($user, 'Registered successfully', 201);
}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->unauthorized('Invalid credentials');
        }

        $request->session()->regenerate();

        // login method in AuthController
        $user = Auth::user();

        return $this->success([
        'user' => $user,
        'email_verified' => !is_null($user->email_verified_at),
        'email' => $user->email, // Add this
        ], 'Authenticated');
    }
    
    public function resendVerificationEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return $this->error('User not found', 404);
    }

    if ($user->hasVerifiedEmail()) {
        return $this->success(null, 'Email already verified.');
    }

    $user->sendEmailVerificationNotification();

    return $this->success(null, 'Verification email resent.');
}

    public function verifyEmail(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success(null, 'Email already verified.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->success(null, 'Email successfully verified.');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success(null, 'Logged out');
    }

    public function getUser(Request $request)
    {
        return $this->success($request->user());
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        // Placeholder: send password reset link via notifications
        return $this->success(null, 'Password reset link sent (placeholder)');
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        // Placeholder: implement password reset workflow
        return $this->success(null, 'Password has been reset (placeholder)');
    }
}
