<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'explanation',
        'code',
        'account',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'code' => 'string',
            'account' => 'string',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
