<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_number',
        'card_name',
        'holder_name',
        'expiry_date',
        'status',
        'daily_limit',
        'monthly_limit',
        'balance',
        'card_type',
    ];

    protected function casts(): array
    {
        return [
            'daily_limit' => 'decimal:8',
            'monthly_limit' => 'decimal:8',
            'balance' => 'decimal:8',
            'status' => 'string',
            'card_type' => 'string',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
