<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KYCSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'document_front',
        'document_back',
        'selfie',
        'country',
        'date_of_birth',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'document_type' => 'string',
            'status' => 'string',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
