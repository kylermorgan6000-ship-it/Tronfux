<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'apy',
        'min_investment',
        'duration',
        'type',
        'featured',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'apy' => 'decimal:8',
            'min_investment' => 'decimal:8',
            'duration' => 'integer',
            'type' => 'string',
            'featured' => 'boolean',
        ];
    }
}
