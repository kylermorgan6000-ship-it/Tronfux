<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('broker_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_user_id')->constrained('broker_users')->cascadeOnDelete();
            $table->string('type'); // deposit, withdrawal, transfer
            $table->decimal('amount', 18, 8);
            $table->string('currency', 10)->default('USD');
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broker_transactions');
    }
};