<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrokerWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'broker_user_id', 'amount', 'currency', 'withdrawal_method', 'destination', 'status'
    ];

    public function user() {
        return $this->belongsTo(BrokerUser::class, 'broker_user_id');
    }
}