<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvestmentPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:investment_plans,name|max:255',
            'description' => 'nullable|string|max:1000',
            'min_amount' => 'required|numeric|min:0.01',
            'max_amount' => 'required|numeric|min:0.01|gt:min_amount',
            'duration_days' => 'required|integer|min:1',
            'return_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ];
    }
}
