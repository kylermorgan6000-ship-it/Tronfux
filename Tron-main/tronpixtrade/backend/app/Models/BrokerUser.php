<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BrokerUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'country', 'status', 'balance', 'pin'
    ];

    protected $hidden = ['password', 'pin'];

    public function transactions() {
        return $this->hasMany(BrokerTransaction::class);
    }

    public function transfers() {
        return $this->hasMany(BrokerTransfer::class);
    }

    public function withdrawals() {
        return $this->hasMany(BrokerWithdrawal::class);
    }
}