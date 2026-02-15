<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvestmentPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|unique:investment_plans,name,' . $this->route('id') . '|max:255',
            'description' => 'nullable|string|max:1000',
            'min_amount' => 'nullable|numeric|min:0.01',
            'max_amount' => 'nullable|numeric|min:0.01',
            'duration_days' => 'nullable|integer|min:1',
            'return_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ];
    }
}
