<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('copied_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('trader_id')->constrained('trader_profiles')->onDelete('cascade');
            $table->enum('status', ['active', 'paused', 'stopped'])->default('active');
            $table->decimal('pnl', 18, 8)->default(0);
            $table->decimal('roi', 10, 8)->default(0);
            $table->json('config')->nullable();
            $table->integer('max_positions')->nullable();
            $table->decimal('stop_loss', 10, 8)->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('trader_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copied_trades');
    }
};
