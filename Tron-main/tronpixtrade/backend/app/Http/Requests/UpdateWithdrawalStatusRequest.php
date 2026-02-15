<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWithdrawalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:approved,rejected',
            'reason' => 'required_if:status,rejected|nullable|string|max:500',
        ];
    }
}
