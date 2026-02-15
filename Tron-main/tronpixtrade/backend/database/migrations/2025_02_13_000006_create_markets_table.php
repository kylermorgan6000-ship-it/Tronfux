<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->string('symbol')->primary();
            $table->string('pair');
            $table->decimal('price', 18, 8);
            $table->decimal('change_24h', 10, 8)->nullable();
            $table->decimal('volume', 18, 8)->nullable();
            $table->string('logo')->nullable();
            $table->integer('leverage')->nullable();
            $table->enum('category', ['spot', 'futures', 'forex'])->default('spot');
            $table->timestamps();
            
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
