<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrokerTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'broker_user_id', 'amount', 'bank_name', 'account_name', 'account_number', 'routing_number', 'bank_address', 'status'
    ];

    public function user() {
        return $this->belongsTo(BrokerUser::class, 'broker_user_id');
    }
}