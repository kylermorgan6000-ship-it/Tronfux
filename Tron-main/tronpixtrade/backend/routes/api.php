<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC/NON-AUTHENTICATED ROUTES
// ============================================

// Authentication routes
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/forgot-password', [\App\Http\Controllers\AuthController::class, 'forgotPassword']);
Route::post('/password-reset', [\App\Http\Controllers\AuthController::class, 'resetPassword']);
Route::post('/email/verification-notification', [\App\Http\Controllers\AuthController::class, 'resendVerificationEmail']);
Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\AuthController::class, 'verifyEmail'])
     ->name('verification.verify');

// CSRF token endpoint
Route::get('/csrf-token', function (Request $request) {
    return response()->json(['csrf_token' => csrf_token()]);
})->middleware('web');

// Public market routes
Route::get('/markets', [\App\Http\Controllers\MarketController::class, 'index']);
Route::get('/markets/{symbol}', [\App\Http\Controllers\MarketController::class, 'show']);
Route::get('/markets/categories', [\App\Http\Controllers\MarketController::class, 'categories']);

// Public traders list
Route::get('/traders', [\App\Http\Controllers\TraderController::class, 'index']);
Route::get('/traders/{id}', [\App\Http\Controllers\TraderController::class, 'show']);

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});

// ============================================
// AUTHENTICATED ROUTES (auth:sanctum)
// ============================================

Route::middleware('auth:sanctum')->group(function () {

    // User profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/profile/update', [\App\Http\Controllers\UserController::class, 'updateProfile']);
    Route::post('/password/change', [\App\Http\Controllers\UserController::class, 'changePassword']);

    // -------- WALLET ROUTES --------
    Route::get('/wallets', [\App\Http\Controllers\WalletController::class, 'index']);
    Route::post('/wallets', [\App\Http\Controllers\WalletController::class, 'store']);
    Route::get('/wallets/{id}', [\App\Http\Controllers\WalletController::class, 'show']);
    Route::put('/wallets/{id}', [\App\Http\Controllers\WalletController::class, 'update']);
    Route::get('/wallets/{id}/balance', [\App\Http\Controllers\WalletController::class, 'balance']);

    // -------- BANK ACCOUNT ROUTES --------
    Route::get('/bank-accounts', [\App\Http\Controllers\BankAccountController::class, 'index']);
    Route::post('/bank-accounts', [\App\Http\Controllers\BankAccountController::class, 'store']);
    Route::get('/bank-accounts/{id}', [\App\Http\Controllers\BankAccountController::class, 'show']);
    Route::put('/bank-accounts/{id}', [\App\Http\Controllers\BankAccountController::class, 'update']);
    Route::delete('/bank-accounts/{id}', [\App\Http\Controllers\BankAccountController::class, 'destroy']);
    Route::get('/bank-accounts/{id}/balance', [\App\Http\Controllers\BankAccountController::class, 'getBalance']);
    Route::get('/bank-accounts/{id}/transactions', [\App\Http\Controllers\BankAccountController::class, 'getTransactionHistory']);

    // -------- DEPOSIT ROUTES --------
    Route::get('/deposits', [\App\Http\Controllers\DepositController::class, 'index']);
    Route::post('/deposits', [\App\Http\Controllers\DepositController::class, 'store']);
    Route::get('/deposits/{deposit}', [\App\Http\Controllers\DepositController::class, 'show']);

    // -------- WITHDRAWAL ROUTES --------
    Route::get('/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'index']);
    Route::post('/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'store']);
    Route::get('/withdrawals/{id}', [\App\Http\Controllers\WithdrawalController::class, 'show']);
    Route::post('/withdrawals/verify-pin', [\App\Http\Controllers\WithdrawalController::class, 'verifyPin']);
    Route::put('/withdrawals/{id}/status', [\App\Http\Controllers\WithdrawalController::class, 'updateStatus'])->middleware('admin');

    // -------- CARDS --------
    Route::get('/cards', [\App\Http\Controllers\CardController::class, 'index']);
    Route::post('/cards', [\App\Http\Controllers\CardController::class, 'store']);
    Route::get('/cards/{id}', [\App\Http\Controllers\CardController::class, 'show']);
    Route::put('/cards/{id}', [\App\Http\Controllers\CardController::class, 'update']);
    Route::patch('/cards/{id}/status', [\App\Http\Controllers\CardController::class, 'toggleStatus'])->middleware('admin');
    Route::get('/admin/cards', [\App\Http\Controllers\CardController::class, 'adminIndex'])->middleware('admin');

    // -------- BROKER WITHDRAWALS --------
    Route::post('/admin/broker-withdrawals', [\App\Http\Controllers\BrokerWithdrawalController::class, 'store']);

    // -------- BROKER TRANSFERS --------
    Route::post('/admin/broker-transfers', [\App\Http\Controllers\BrokerTransferController::class, 'store']);

    // -------- BROKER KYC --------
    Route::post('/admin/broker-users/kyc/submit', [\App\Http\Controllers\BrokerKYCController::class, 'submit']);
    Route::get('/admin/broker-users', [\App\Http\Controllers\BrokerKYCController::class, 'index']);
    Route::post('/admin/broker-users/{id}/kyc/approve', [\App\Http\Controllers\BrokerKYCController::class, 'approve']);
    Route::post('/admin/broker-users/{id}/kyc/reject', [\App\Http\Controllers\BrokerKYCController::class, 'reject']);
    Route::post('/admin/broker-users/{id}/kyc/delete', [\App\Http\Controllers\BrokerKYCController::class, 'delete']);
    Route::post('/admin/broker-users/credit', [\App\Http\Controllers\BrokerUserController::class, 'credit']);
    Route::post('/admin/broker-users/deduct', [\App\Http\Controllers\BrokerUserController::class, 'deduct']);

    // -------- INVESTMENT PLANS (BROKER/USER) --------
    Route::get('/admin/broker-plans', [\App\Http\Controllers\BrokerPlanController::class, 'index']);
    Route::post('/admin/broker-plans', [\App\Http\Controllers\BrokerPlanController::class, 'store'])->middleware('admin');
    Route::put('/admin/broker-plans/{id}', [\App\Http\Controllers\BrokerPlanController::class, 'update'])->middleware('admin');
    Route::delete('/admin/broker-plans/{id}', [\App\Http\Controllers\BrokerPlanController::class, 'destroy'])->middleware('admin');

    // -------- SECURITY STEPS --------
    Route::get('/security-steps', [\App\Http\Controllers\SecurityStepController::class, 'index']);
    Route::post('/security-steps', [\App\Http\Controllers\SecurityStepController::class, 'store'])->middleware('admin');
    Route::put('/security-steps/{id}', [\App\Http\Controllers\SecurityStepController::class, 'update'])->middleware('admin');
    Route::delete('/security-steps/{id}', [\App\Http\Controllers\SecurityStepController::class, 'destroy'])->middleware('admin');
    Route::post('/security-steps/{id}/verify', [\App\Http\Controllers\SecurityStepController::class, 'verify']);

    // -------- USER KYC SUBMISSION --------
    Route::get('/kyc', [\App\Http\Controllers\KYCSubmissionController::class, 'index'])->middleware('admin');
    Route::post('/kyc', [\App\Http\Controllers\KYCSubmissionController::class, 'store']);
    Route::get('/kyc/{id}', [\App\Http\Controllers\KYCSubmissionController::class, 'show']);
    Route::put('/kyc/{id}', [\App\Http\Controllers\KYCSubmissionController::class, 'update'])->middleware('admin');
    Route::delete('/kyc/{id}', [\App\Http\Controllers\KYCSubmissionController::class, 'destroy'])->middleware('admin');

    // -------- ADMIN ROUTES --------
    Route::middleware('admin')->group(function () {
        Route::get('/admin/statistics', [\App\Http\Controllers\AdminController::class, 'statistics']);
        Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'users']);
        Route::get('/admin/kyc', [\App\Http\Controllers\AdminController::class, 'kyc']);
        Route::get('/admin/bank-users', [\App\Http\Controllers\AdminController::class, 'bankUsers']);
        Route::get('/admin/broker-trades', [\App\Http\Controllers\AdminController::class, 'brokerTrades']);
        Route::get('/admin/wallets', [\App\Http\Controllers\AdminController::class, 'wallets']);
        Route::get('/admin/broker-transactions', [\App\Http\Controllers\BrokerTransactionController::class, 'index']);
        Route::get('/copied-trades', [\App\Http\Controllers\CopiedTradeController::class, 'adminIndex']);
        Route::post('/copied-trades/{id}/close', [\App\Http\Controllers\CopiedTradeController::class, 'close']);
        Route::post('/admin/users/credit', [\App\Http\Controllers\AdminController::class, 'creditUser']);
        Route::post('/admin/users/deduct', [\App\Http\Controllers\AdminController::class, 'deductUser']);
        Route::post('/admin/users/{userId}/kyc/approve', [\App\Http\Controllers\AdminController::class, 'approveKyc']);
        Route::post('/admin/users/{userId}/kyc/reject', [\App\Http\Controllers\AdminController::class, 'rejectKyc']);
        Route::post('/admin/users/{userId}/kyc/delete', [\App\Http\Controllers\AdminController::class, 'deleteKyc']);
        Route::post('/admin/users/{userId}/status', [\App\Http\Controllers\AdminController::class, 'updateUserStatus']);
        Route::delete('/admin/users/{userId}', [\App\Http\Controllers\AdminController::class, 'deleteUser']);
    });
});