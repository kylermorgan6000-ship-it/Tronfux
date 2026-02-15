<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'avatar',
        'roi',
        'pnl',
        'pnl_7d',
        'followers',
        'risk_level',
        'win_rate',
        'max_drawdown',
        'trade_count',
    ];

    protected function casts(): array
    {
        return [
            'roi' => 'decimal:8',
            'pnl' => 'decimal:8',
            'pnl_7d' => 'decimal:8',
            'followers' => 'integer',
            'risk_level' => 'string',
            'win_rate' => 'decimal:8',
            'max_drawdown' => 'decimal:8',
            'trade_count' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function copiedTrades()
    {
        return $this->hasMany(CopiedTrade::class, 'trader_id');
    }
}
