<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCopiedTradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trader_profile_id' => 'required|exists:trader_profiles,id',
            'initial_investment' => 'required|numeric|min:0.01',
            'max_positions' => 'nullable|integer|min:1',
            'stop_loss_percentage' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
