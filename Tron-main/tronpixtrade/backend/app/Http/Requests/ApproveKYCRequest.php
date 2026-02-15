<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveKYCRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [];
    }
}
