<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTraderProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => 'nullable|string|max:1000',
            'risk_level' => 'nullable|string|in:low,medium,high',
            'min_investment' => 'nullable|numeric|min:0',
            'return_percentage' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
