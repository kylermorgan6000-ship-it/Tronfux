<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    use HasFactory;

    protected $primaryKey = 'symbol';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'symbol',
        'pair',
        'price',
        'change_24h',
        'volume',
        'logo',
        'leverage',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:8',
            'change_24h' => 'decimal:8',
            'volume' => 'decimal:8',
            'leverage' => 'integer',
            'category' => 'string',
        ];
    }
}
