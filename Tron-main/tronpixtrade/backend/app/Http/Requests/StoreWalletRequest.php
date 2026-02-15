<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency' => 'required|string|max:10|unique:wallets,currency,NULL,id,user_id,' . auth()->id(),
            'initial_balance' => 'nullable|numeric|min:0',
        ];
    }
}
