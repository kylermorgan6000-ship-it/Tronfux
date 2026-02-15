<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKYCSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type' => 'required|string|in:passport,national_id,drivers_license',
            'document_number' => 'required|string|max:50',
            'document_front' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'document_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'selfie' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
        ];
    }
}
