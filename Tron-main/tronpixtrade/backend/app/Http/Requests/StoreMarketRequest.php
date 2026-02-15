<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'symbol' => 'required|string|unique:markets,symbol|max:20',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'current_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }
}
