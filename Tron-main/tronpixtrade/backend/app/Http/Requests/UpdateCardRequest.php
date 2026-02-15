<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->id === $this->card->user_id || auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|string|in:pending,approved,rejected,blocked',
            'is_default' => 'nullable|boolean',
        ];
    }
}
