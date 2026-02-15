<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class VerifiedUserSeeder extends Seeder
{
    public function run(): void
    {
        // Broker user
        User::create([
            'name' => 'Verified Broker',
            'email' => 'verified-broker@example.com',
            'password' => Hash::make('password123'), // use a secure password
            'account_type' => 'broker',
            'phone' => '08012345678',
            'country' => 'NG',
            'status' => 'active',
            'kyc_status' => 'approved',
            'two_factor_enabled' => false,
            'email_verified_at' => now(), // mark as verified
        ]);

        // Banking user
        User::create([
            'name' => 'Verified Banking User',
            'email' => 'verified-banking@example.com',
            'password' => Hash::make('password123'), // use a secure password
            'account_type' => 'banking',
            'phone' => '08087654321',
            'country' => 'NG',
            'status' => 'active',
            'kyc_status' => 'approved',
            'two_factor_enabled' => false,
            'email_verified_at' => now(), // mark as verified
        ]);

        // Admin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'), // secure admin password
            'account_type' => 'admin',
            'phone' => '08099998888',
            'country' => 'NG',
            'status' => 'active',
            'kyc_status' => 'approved',
            'two_factor_enabled' => true,
            'email_verified_at' => now(), // mark as verified
        ]);

        $this->command->info('Verified broker user seeded: verified-broker@example.com / password123');
        $this->command->info('Verified banking user seeded: verified-banking@example.com / password123');
        $this->command->info('Admin user seeded: admin@example.com / admin123');
    }
}