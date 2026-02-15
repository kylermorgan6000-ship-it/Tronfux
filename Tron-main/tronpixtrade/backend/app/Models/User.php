<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'account_type',
        'phone',
        'country',
        'kyc_status',
        'two_factor_enabled',
        'two_factor_secret',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'account_type' => 'string',
            'kyc_status' => 'string',
            'status' => 'string',
        ];
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function kycSubmission()
    {
        return $this->hasOne(KYCSubmission::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function traderProfile()
    {
        return $this->hasOne(TraderProfile::class);
    }

    public function copiedTrades()
    {
        return $this->hasMany(CopiedTrade::class);
    }

    public function securitySteps()
    {
        return $this->hasMany(SecurityStep::class);
    }
}
