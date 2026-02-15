<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('card_number');
            $table->string('card_name');
            $table->string('holder_name');
            $table->integer('expiry_month');
            $table->integer('expiry_year');
            $table->string('cvv');
            $table->enum('status', ['active', 'blocked', 'expired'])->default('active');
            $table->decimal('daily_limit', 18, 8);
            $table->decimal('monthly_limit', 18, 8);
            $table->decimal('balance', 18, 8)->default(0);
            $table->enum('card_type', ['debit', 'credit'])->default('debit');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
