<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Models\Card;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CardController extends ApiController
{
    /**
     * List user cards
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Card::where('user_id', auth()->id());

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('card_type')) {
                $query->where('card_type', $request->card_type);
            }

            $cards = $query->paginate($request->per_page ?? 15);

            return $this->successWithPagination($cards, 'Cards retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve cards: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Admin list all cards
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = Card::query();

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('card_type')) {
                $query->where('card_type', $request->card_type);
            }

            $cards = $query->with('user')->paginate($request->per_page ?? 15);

            return $this->successWithPagination($cards, 'Cards retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve cards: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create/Apply for card
     */
   public function store(StoreCardRequest $request): JsonResponse
{
    try {
        // Fetch user info
        $user = auth()->user();

        // Generate card details automatically
        $cardNumber = $this->generateCardNumber($request->card_type);
        $cvv = $this->generateCVV();
        $expiryMonth = rand(1, 12);
        $expiryYear = date('Y') + rand(2, 5);

        $card = Card::create([
            'user_id' => $user->id,
            'bank_account_id' => $request->bank_account_id,
            'card_type' => $request->card_type,
            'card_number' => $this->encryptCardNumber($cardNumber),
            'cardholder_name' => $user->name,
            'expiry_month' => $expiryMonth,
            'expiry_year' => $expiryYear,
            'cvv' => $this->encryptCVV($cvv),
            'status' => 'pending',
            'daily_limit' => $request->daily_limit,
            'monthly_limit' => $request->monthly_limit,
        ]);

        return $this->success($card, 'Card application submitted successfully', 201);

    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to create card: ' . $e->getMessage()], 500);
    }
}

/**
 * Generate card number based on card type
 */
private function generateCardNumber(string $type): string
{
    $prefix = match($type) {
        'debit' => '4000', // Visa debit
        'credit' => '5000', // Mastercard credit
        default => '4000',
    };
    return $prefix . rand(100000000000, 999999999999); // 16 digits
}

/**
 * Generate 3-digit CVV
 */
private function generateCVV(): string
{
    return str_pad(strval(rand(0, 999)), 3, '0', STR_PAD_LEFT);
}

    /**
     * Get card details
     */
    public function show($id): JsonResponse
    {
        try {
            $card = Card::find($id);

            if (!$card) {
                return $this->notFound('Card not found');
            }

            // Check authorization
            if ($card->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this card');
            }

            return $this->success($card, 'Card retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve card: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update card status
     */
    public function update(UpdateCardRequest $request, $id): JsonResponse
    {
        try {
            $card = Card::find($id);

            if (!$card) {
                return $this->notFound('Card not found');
            }

            // Only admin can update card status
            if (!auth()->user()->is_admin && $request->has('status')) {
                return $this->forbidden('Only admins can update card status');
            }

            $card->update($request->only(['status', 'is_default']));

            return $this->success(['card' => $card], 'Card updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update card: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete card
     */
    public function destroy($id): JsonResponse
    {
        try {
            $card = Card::find($id);

            if (!$card) {
                return $this->notFound('Card not found');
            }

            // Check authorization
            if ($card->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have permission to delete this card');
            }

            // Delete associated files
            if ($card->document_front && file_exists(storage_path('app/public/' . $card->document_front))) {
                unlink(storage_path('app/public/' . $card->document_front));
            }
            if ($card->document_back && file_exists(storage_path('app/public/' . $card->document_back))) {
                unlink(storage_path('app/public/' . $card->document_back));
            }

            $card->delete();

            return $this->success(null, 'Card deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete card: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Encrypt card number
     */
    private function encryptCardNumber($cardNumber): string
    {
        // Implement encryption logic using Laravel's encryption
        return encrypt($cardNumber);
    }

    /**
     * Toggle card status
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $card = Card::find($id);

            if (!$card) {
                return $this->notFound('Card not found');
            }

            // Check authorization
            if ($card->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have permission to update this card');
            }

            $card->status = $card->status === 'active' ? 'inactive' : 'active';
            $card->save();

            return $this->success(['card' => $card], 'Card status updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update card status: ' . $e->getMessage(), 500);
        }
    }
