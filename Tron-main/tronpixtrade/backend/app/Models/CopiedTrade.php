<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CopiedTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trader_id',
        'status',
        'pnl',
        'roi',
        'config',
        'max_positions',
        'stop_loss',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'pnl' => 'decimal:8',
            'roi' => 'decimal:8',
            'config' => 'json',
            'max_positions' => 'integer',
            'stop_loss' => 'decimal:8',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function traderProfile()
    {
        return $this->belongsTo(TraderProfile::class, 'trader_id');
    }
}
