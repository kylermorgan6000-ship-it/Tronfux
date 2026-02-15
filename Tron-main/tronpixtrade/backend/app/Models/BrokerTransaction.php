<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrokerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'broker_user_id', 'type', 'amount', 'currency', 'status'
    ];

    public function user() {
        return $this->belongsTo(BrokerUser::class, 'broker_user_id');
    }
}