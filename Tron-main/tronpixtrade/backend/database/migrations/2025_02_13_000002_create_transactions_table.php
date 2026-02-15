<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('type', ['deposit', 'withdraw', 'transfer', 'trade', 'fee', 'credit_admin', 'debit_admin', 'credit', 'debit']);
            $table->decimal('amount', 18, 8);
            $table->string('currency');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('wallet_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
