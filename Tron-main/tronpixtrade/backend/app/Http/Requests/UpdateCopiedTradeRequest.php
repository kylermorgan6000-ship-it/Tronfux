<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCopiedTradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->id === $this->copiedTrade->user_id || auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'max_positions' => 'nullable|integer|min:1',
            'stop_loss_percentage' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
