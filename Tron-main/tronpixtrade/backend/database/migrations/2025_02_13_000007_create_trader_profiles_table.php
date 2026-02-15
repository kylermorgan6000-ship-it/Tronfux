<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trader_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->decimal('roi', 10, 8)->default(0);
            $table->decimal('pnl', 18, 8)->default(0);
            $table->decimal('pnl_7d', 18, 8)->default(0);
            $table->integer('followers')->default(0);
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium');
            $table->decimal('win_rate', 5, 2)->default(0);
            $table->decimal('max_drawdown', 10, 8)->default(0);
            $table->integer('trade_count')->default(0);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('risk_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trader_profiles');
    }
};
