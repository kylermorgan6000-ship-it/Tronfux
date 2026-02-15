<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminStats extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'total_users',
        'active_users',
        'total_broker_trades',
        'total_wallet_balance',
        'daily_trading_volume',
        'fees_earned',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'total_users' => 'integer',
            'active_users' => 'integer',
            'total_broker_trades' => 'integer',
            'total_wallet_balance' => 'decimal:8',
            'daily_trading_volume' => 'decimal:8',
            'fees_earned' => 'decimal:8',
            'updated_at' => 'datetime',
        ];
    }
}
