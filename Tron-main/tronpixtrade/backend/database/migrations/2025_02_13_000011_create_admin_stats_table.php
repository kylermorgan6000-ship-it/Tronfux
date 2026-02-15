<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('total_users')->default(0);
            $table->integer('active_users')->default(0);
            $table->integer('total_broker_trades')->default(0);
            $table->decimal('total_wallet_balance', 18, 8)->default(0);
            $table->decimal('daily_trading_volume', 18, 8)->default(0);
            $table->decimal('fees_earned', 18, 8)->default(0);
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_stats');
    }
};
