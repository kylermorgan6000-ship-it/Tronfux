<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === (int) $this->route('id') || auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $this->route('id'),
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
        ];
    }
}
