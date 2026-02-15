<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('broker_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_user_id')->constrained('broker_users')->cascadeOnDelete();
            $table->decimal('amount', 18, 8);
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('routing_number')->nullable();
            $table->string('bank_address')->nullable();
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broker_transfers');
    }
};