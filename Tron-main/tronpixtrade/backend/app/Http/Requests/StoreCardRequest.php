<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'bank_account_id' =>
        ['required', 'exists:bank_accounts,id'],
        'card_type' => 
        ['required', 'string', 'in:debit,credit'],
        'daily_limit' =>
        ['required', 'numeric', 'min:0'],
        'monthly_limit' => 
        ['required', 'numeric', 'min:0'],
        // Remove all required validations for card_number, cvv, expiry, document_front/back
    ];
}
}
