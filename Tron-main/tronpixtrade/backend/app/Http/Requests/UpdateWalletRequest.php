<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->id === $this->wallet->user_id || auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'nickname' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ];
    }
}
