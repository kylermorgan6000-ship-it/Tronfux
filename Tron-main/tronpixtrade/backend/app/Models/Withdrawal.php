<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'amount',
        'status',
        'destination_address',
        'method',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:8',
            'status' => 'string',
            'metadata' => 'json',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
